<table class="table table-bordered table-sm">
  <tbody>
    <tr><th>Created</th><td>{{ dateFormat($row->created_at, 'd-M-Y h:i A') }}</td></tr>
    <tr><th>Category Name</th><td>{{ $row->category_name }}</td></tr>
  </tbody>
</table>
