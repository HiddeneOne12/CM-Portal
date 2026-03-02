<table class="table table-bordered table-sm">
  <tbody>
    <tr><th>Created</th><td>{{ dateFormat($row->created_at, 'd-M-Y h:i A') }}</td></tr>
    <tr><th>Module Name</th><td>{{ $row->module_name }}</td></tr>
    <tr><th>Category</th><td>{{ $row->category->category_name ?? '-' }}</td></tr>
    <tr><th>Slug</th><td>{{ $row->route }}</td></tr>
    <tr><th>CSS Class</th><td>{{ $row->css_class ?? '-' }}</td></tr>
  </tbody>
</table>
