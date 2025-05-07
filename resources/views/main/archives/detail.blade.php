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
                        <td class="left" colspan="3">
                            <div style="display: flex; gap: 40px; align-items: center;">
                                <div style="text-align: center;">
                                    <a href="{{ asset('uploads/main/arsip/' . $row->file) }}" target="_blank">
                                        <i class="fa fa-file-pdf fa-4x text-danger"></i><br>
                                        <span>File Asli</span>
                                    </a>
                                </div>
                                <div style="text-align: center;">
                                    <a href="{{ asset('uploads/main/arsip/' . $row->hasil_pdf) }}" target="_blank">
                                        <i class="fa fa-file-pdf fa-4x text-success"></i><br>
                                        <span>File Pembubuhan</span>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="left" width="135px">Nomor Arsip</td>
                        <td width="10px"> : </td>
                        <th class="left"><?php echo $row->number; ?></th>
                    </tr>

                    <tr>
                        <td class="left">Referensi Nomor Arsip</td>
                        <td> : </td>
                        <th class="left">
                            <?php echo $row->ref_number; ?>
                        </th>
                    </tr>
                    <tr>
                        <td class="left">Tanggal Arsip</td>
                        <td> : </td>
                        <th class="left"><?php echo date('d F Y H:i', strtotime($row->date)); ?>
                        </th>
                    </tr>
                    <tr>
                        <td class="left">Nama Arsip</td>
                        <td> : </td>
                        <th class="left"><?php echo $row->title; ?></th>
                    </tr>
                    <tr>
                        <td class="left">kategori</td>
                        <td> : </td>
                        <th class="left"><?php echo $row->category_name; ?></th>
                    </tr>
                    <tr>
                        <td class="left">lokasi</td>
                        <td> : </td>
                        <th class="left"><?php echo $row->location_name; ?></th>
                    </tr>
                    <tr>
                        <td class="left">tags</td>
                        <td> : </td>
                        <th class="left">
                            @foreach ($tags as $uk)
                                {{ $uk->name }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </th>
                    </tr>
                    <tr>
                        <td class="left">Keterangan</td>
                        <td> : </td>
                        <th class="left"><?php echo $row->description; ?></th>
                    </tr>
                    <tr>
                        <td class="left">Catatan Arsip</td>
                        <td> : </td>
                        <th class="left"><?php echo $row->note; ?></th>
                    </tr>
                    <tr>
                        <td class="left">Status</td>
                        <td> : </td>
                        <th class="left"><?php echo $row->status == 1 ? 'Active' : 'Non Active'; ?></th>
                    </tr>
                    <tr>
                        <td class="left">Dibuat pada</td>
                        <td> :</td>
                        <th class="left"><?php echo $row->created_at; ?></th>
                    </tr>
                    <tr>
                        <td class="left">Dibuat oleh</td>
                        <td> :</td>
                        <th class="left"><?php echo $row->created_from; ?></th>
                    </tr>
                    <?php if ($row->updated_at) { ?>
                    <tr>
                        <td class="left">Diperbarui pada</td>
                        <td> :</td>
                        <th class="left"><?php echo $row->updated_at; ?></th>
                    </tr>
                    <tr>
                        <td class="left">Diperbarui oleh</td>
                        <td> :</td>
                        <th class="left"><?php echo $row->updated_from; ?></th>
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
