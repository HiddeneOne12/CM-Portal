   jQuery(document).ready(function() {
        jQuery("#loader-wrapper").remove();
    });
 
 /*Notification.requestPermission(function(status) {
    console.log('Notification permission status:', status);
});
*/
function displayNotification() {
  if (Notification.permission == 'granted') {
    navigator.serviceWorker.getRegistration().then(function(reg) {
      var options = {
        body: 'Here is a notification body!',
        icon: '{{asset('/')}}/src/images/icons/144.png',
        vibrate: [100, 50, 100],
        data: {
          dateOfArrival: Date.now(),
          primaryKey: 1
        }
      };
      reg.showNotification('Hello world!,', options);
    });
  }
}

//displayNotification();

    function myFunction() {
  var x = document.getElementById("myLinks");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}
 

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  var firebaseConfig = {
    apiKey: "AIzaSyAMyfi54kQX1583JH6ah39VmX5ZbWt3STs",
    authDomain: "happinessc-f4e79.firebaseapp.com",
    databaseURL: "https://happinessc-f4e79-default-rtdb.firebaseio.com",
    projectId: "happinessc-f4e79",
    storageBucket: "happinessc-f4e79.appspot.com",
    messagingSenderId: "555598707305",
    appId: "1:555598707305:web:90c93692b03a927557c0f8",
    measurementId: "G-4C0C3VFWYY"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  firebase.analytics();

  //------
  const messaging = firebase.messaging();
    messaging
   .requestPermission()
   .then(function () {
     
     console.log("Notification permission granted.");

     // get the token in the form of promise
     return messaging.getToken()
   })
   .then(function(token) {
     // print the token on the HTML page
     $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

     $.ajax({
              type: "POST",
              url: '{{asset('/add-token')}}',
              data: "token=" + token
            });
     console.log("token is : " + token);
   })
   .catch(function (err) {
   //ErrElem.innerHTML = ErrElem.innerHTML + "; " + err
   console.log("Unable to get permission to notify.", err);
 });
 
messaging.onMessage((payload) => {
  console.log('Message received. ', payload);
});
