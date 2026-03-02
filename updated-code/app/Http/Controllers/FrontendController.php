<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Documentation;
use App\Models\Event;
use App\Models\Hero;
use App\Models\Interview;
use App\Models\PortalUser;
use App\Models\Report;
use App\Models\Training;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class FrontendController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;


    public function index(): View
    {
        return view('frontend.home');
    }

    public function showLogin(): View|RedirectResponse
    {
        return view('frontend.login');
    }

    public function requestOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);
    
        $exists = PortalUser::where('email', $request->input('email'))->where('status', 1)->exists();
        if (!$exists) {
            return redirect()->route('members-portal')
                ->with('error', 'User doesn\'t exist.')
                ->withInput($request->only('email'));
        }
    
        $email = $request->input('email');
        $otp = (string) random_int(10000000, 99999999);
        $request->session()->put('otp_email', $email);
        $request->session()->put('otp_code', $otp);
        $request->session()->put('otp_expires_at', now()->addMinutes(self::OTP_EXPIRY_MINUTES)->timestamp);
    
        try {
            Mail::to($email)->send(new OtpMail($otp));
        } catch (\Throwable $e) {
            return redirect()->route('members-portal')
                ->with('error', 'We could not send the OTP. Please try again later.')
                ->withInput($request->only('email'));
        }
    
        return redirect()->route('members-portal.otp')->with('success', 'OTP sent to your email.');
    }

    public function showOtp(Request $request): View|RedirectResponse
    {
        if (!session()->has('otp_email')) {
            return redirect()->route('members-portal')
                ->with('error', 'Please enter your email first to receive OTP.');
        }
        return view('frontend.otp');
    }
    

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|string|size:8|regex:/^[0-9]{8}$/',
        ], ['otp.regex' => 'OTP must be 8 digits.']);
    
        if (!session()->has('otp_email') || !session()->has('otp_code') || !session()->has('otp_expires_at')) {
            return redirect()->route('members-portal')
                ->with('error', 'Session expired. Please request a new OTP.');
        }
    
        $expiresAt = session('otp_expires_at');
        if ($expiresAt && time() > $expiresAt) {
            $request->session()->forget(['otp_code', 'otp_expires_at']);
            return redirect()->route('members-portal')
                ->with('error', 'OTP has expired. Please request a new one.');
        }
    
        $entered = preg_replace('/\D/', '', $request->input('otp'));
        $stored = (string) session('otp_code');
    
        if ($entered !== $stored) {
            return redirect()->route('members-portal.otp')
                ->with('error', 'Invalid OTP. Please try again.')
                ->withInput($request->only('otp'));
        }
    
// Clear OTP session values
        $request->session()->forget(['otp_code', 'otp_expires_at']);
        $request->session()->regenerate();
        $request->session()->put('portal_authenticated', true);
        $request->session()->put('portal_ip', $request->ip());
        $request->session()->put('portal_user_agent', substr($request->userAgent(), 0, 255));
    
        return redirect()->route('portal')
            ->with('success', 'Welcome to the Members\' Portal.');
    }

    public function showPortal(): View|RedirectResponse
    {
       
        $hero = Hero::enabled()->latest('id')->first();
        return view('frontend.portal', compact('hero'));
    }

    public function showInterviews(): View|RedirectResponse
    {
       
        $interviews = Interview::enabled()->orderBy('id')->paginate(6);
        return view('frontend.interviews', compact('interviews'));
    }

    public function loadMoreInterviews(Request $request): JsonResponse
    {
       
        $page = max(1, (int) $request->input('page', 1));
        $interviews = Interview::enabled()->orderBy('id')->paginate(6, ['*'], 'page', $page);
        $html = view('frontend.partials.interview_cards', ['interviews' => $interviews])->render();
        return response()->json([
            'success' => true,
            'html' => $html,
            'has_more' => $interviews->hasMorePages(),
            'next_page' => $interviews->hasMorePages() ? $interviews->currentPage() + 1 : 0,
        ]);
    }

    public function showEventMaterials(Request $request): View|RedirectResponse
    {
       
        $events = Event::enabled()->orderBy('event_date', 'desc')->orderBy('id', 'desc')->paginate(4)->withQueryString();
        return view('frontend.materials', ['events' => $events]);
    }

    public function showEventMaterial(string $token): View|RedirectResponse
    {
       
        $id = decryptIdFromUrl($token);
        if ($id === null) {
            abort(404);
        }
        $event = Event::enabled()->with(['eventImages', 'eventParticipants' => function ($q) {
            $q->whereHas('participant', fn ($p) => $p->where('status', 1))->with(['participant.company', 'eventParticipantDocuments']);
        }])->find($id);
        if (!$event) {
            abort(404);
        }
        return view('frontend.material', ['event' => $event]);
    }

    public function showInterview(string $token): View|RedirectResponse
    {
       
        $id = decryptIdFromUrl($token);
        if ($id === null) {
            abort(404);
        }
        $interview = Interview::enabled()->with(['participant.company'])->find($id);
        if (!$interview) {
            abort(404);
        }
        return view('frontend.interview', ['interview' => $interview]);
    }

    public function showAgenda(): View|RedirectResponse
    {
       
        $upcomingEvent = Event::enabled()->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->with(['company', 'eventParticipants' => function ($q) {
                $q->whereHas('participant', fn ($p) => $p->where('status', 1))->with('participant.company');
            }])
            ->first();
        if (!$upcomingEvent) {
            $upcomingEvent = Event::enabled()->orderBy('id', 'desc')
                ->with(['company', 'eventParticipants' => function ($q) {
                    $q->whereHas('participant', fn ($p) => $p->where('status', 1))->with('participant.company');
                }])
                ->first();
        }
        $reports = Report::enabled()->orderBy('published_in_date', 'desc')->orderBy('id', 'desc')->limit(12)->get();
        return view('frontend.agenda', compact('upcomingEvent', 'reports'));
    }

    public function showReports(): View|RedirectResponse
    {
       
        $reports = Report::enabled()->orderBy('published_in_date', 'desc')->orderBy('id', 'desc')->paginate(3);
        return view('frontend.reports', compact('reports'));
    }

    public function loadMoreReports(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->input('page', 1));
        $reports = Report::enabled()->orderBy('published_in_date', 'desc')->orderBy('id', 'desc')->paginate(3, ['*'], 'page', $page);
        $html = view('frontend.partials.report_cards', ['reports' => $reports])->render();
        return response()->json([
            'success' => true,
            'html' => $html,
            'has_more' => $reports->hasMorePages(),
            'next_page' => $reports->hasMorePages() ? $reports->currentPage() + 1 : 0,
        ]);
    }

    public function showDocumentation(): View|RedirectResponse
    {
        $documentations = Documentation::enabled()
            ->orderBy('published_in_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(6);
        return view('frontend.documentation', compact('documentations'));
    }

    public function loadMoreDocumentation(Request $request): JsonResponse
    {
       
        $page = max(1, (int) $request->input('page', 1));
        $documentations = Documentation::enabled()
            ->orderBy('published_in_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(6, ['*'], 'page', $page);
        $html = view('frontend.partials.documentation_cards', ['documentations' => $documentations])->render();
        return response()->json([
            'success' => true,
            'html' => $html,
            'has_more' => $documentations->hasMorePages(),
            'next_page' => $documentations->hasMorePages() ? $documentations->currentPage() + 1 : 0,
        ]);
    }

    public function showTraining(): View|RedirectResponse
    {
       
        $trainings = Training::enabled()->orderBy('id', 'desc')->paginate(6);
        return view('frontend.training', compact('trainings'));
    }

    public function loadMoreTraining(Request $request): JsonResponse
    {
       
        $page = max(1, (int) $request->input('page', 1));
        $trainings = Training::enabled()->orderBy('id', 'desc')->paginate(6, ['*'], 'page', $page);
        $html = view('frontend.partials.training_cards', ['trainings' => $trainings])->render();
        return response()->json([
            'success' => true,
            'html' => $html,
            'has_more' => $trainings->hasMorePages(),
            'next_page' => $trainings->hasMorePages() ? $trainings->currentPage() + 1 : 0,
        ]);
    }

    public function showTrainingVideo(string $token): View|RedirectResponse
    {



        $id = decryptIdFromUrl($token);
        if ($id === null) {
            abort(404);
        }
        $training = Training::enabled()->where('id', $id)->firstOrFail();
        return view('frontend.training-video', compact('training'));
    }

    public function portalLogout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect()->route('members-portal')
            ->with('success', 'You have signed out.');
    }
}
