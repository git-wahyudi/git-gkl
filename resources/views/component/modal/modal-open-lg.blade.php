<!--modal-->
<form action="{{url_admin($action)}}" method="post" enctype="multipart/form-data" id="{{$id}}" class="form">
@csrf
<div id="modal-{{$id}}" class="modal fade animated" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content pl-3 pr-3 rounded">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-clone mr-2"></i>{!!$title!!}</h5>
      </div>
      <div class="modal-body">