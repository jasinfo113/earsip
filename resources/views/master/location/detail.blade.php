<div id="overlay"></div>
<div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="fw-bolder">{{ $title }}</h2>
            <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" onclick="closeModal()">
                <i class="fa fa-times fs-3"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="d-flex flex-column m-3">
                <table class="table m-table">

                    <tr>
                        <td class="left" width="135px">Lokasi</td>
                        <td width="10px"> : </td>
                        <th class="left"><?php echo $row->name ?></th>
                    </tr>


                    <tr>
                        <td class="left">Keterangan</td>
                        <td> : </td>
                        <th class="left"><?php echo $row->description ?></th>
                    </tr>
                    <tr>
                        <td class="left">Status</td>
                        <td> : </td>
                        <th class="left"><?php echo $row->status == 1 ? 'Active' : 'Non Active'; ?></th>
                    </tr>
                    <tr>
                        <td class="left">Dibuat pada</td>
                        <td> :</td>
                        <th class="left"><?php echo $row->created_at ?></th>
                    </tr>
                    <tr>
                        <td class="left">Dibuat oleh</td>
                        <td> :</td>
                        <th class="left"><?php echo $row->created_from ?></th>
                    </tr>
                    <?php if ($row->updated_at) { ?>
                        <tr>
                            <td class="left">Diperbarui pada</td>
                            <td> :</td>
                            <th class="left"><?php echo $row->updated_at ?></th>
                        </tr>
                        <tr>
                            <td class="left">Diperbarui oleh</td>
                            <td> :</td>
                            <th class="left"><?php echo $row->updated_from ?></th>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <div class="modal-footer flex-end gap-2">
            <button type="button" class="btn btn-light btn-cancel" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>


