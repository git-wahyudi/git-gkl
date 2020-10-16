    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="fa fa-window-close-o mr-1"></i> Keluar
        </button>
        @if($text != '')
        <!-- class submit di button untuk menghilangkan status disable button ketika hide modal -->
        <button type="submit" class="btn btn-default submit">
          <i class="fa fa-check-square-o mr-1"></i> {{ucfirst($text)}}
        </button>
        @endif()
      </div>
    </div>
  </div>
</div>
</form>