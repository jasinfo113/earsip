<div id="overlay"></div>
<form id="form_data" method="post" role="form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
    @csrf


    <div class="modal-dialog modal-dialog-centered mw-950px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">{{ $title }}</h2>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" onclick="closeModal()">
                    <i class="fa fa-times fs-3"></i>
                </button>
            </div>
            <div class="modal-body m-4" id="pdf-container">
                <canvas id="pdf-canvas"></canvas>
                <div id="qr-code" data-x="10" data-y="10"
                    style="position: absolute; top: 10px; left: 10px; width: 100px; height: 100px;"></div>

                <div id="qr-code-wrapper" data-code="{{ $code }}" data-x="10" data-y="10"
                    style="position: absolute; top: 10px; left: 10px; width: 100px; height: 100px; background: transparent;">
                </div>
                <input type="text" name="update" id="namafile" value="{{ $nama_file }}" />
                <input type="hidden" name="id" value="{{ $code }}" />
                <input type="hidden" name="document_id" value="{{ $document_id }}" />
                <div class="modal-footer flex-end gap-2">
                    <button type="button" class="btn btn-light btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Please wait...<span
                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<link rel="stylesheet" href="{{ asset('assets/styles/pembubuhan/jquery-ui.css?v=' . time()) }}" type="text/css" />
<script src="{{ asset('assets/scripts/pembubuhan/jquery-ui.min.js?v=' . time()) }}"></script>
<script src="{{ asset('assets/scripts/pembubuhan/pdf.min.js?v=' . time()) }}"></script>
<script src="{{ asset('assets/scripts/pembubuhan/jspdf.umd.min.js?v=' . time()) }}"></script>
<script src="{{ asset('assets/scripts/pembubuhan/qrcode.min.js?v=' . time()) }}"></script>

<style>
    #pdf-container {
        position: relative;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
    }

    #pdf-canvas {
        display: block;
        max-width: 100%;
        height: auto;
    }


    #qr-code canvas {
        width: 100% !important;
        height: 100% !important;
        background: transparent;
    }
</style>

<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('assets/scripts/pembubuhan/pdf.worker.min.js') }}";

    function loadPDF() {
        const pdfUrl = "{{ $nama_file }}"; // URL file PDF
        const loadingTask = pdfjsLib.getDocument(pdfUrl);

        loadingTask.promise.then(function(pdf) {
            pdf.getPage(1).then(function(page) {
                const scale = 1.5;
                const viewport = page.getViewport({
                    scale
                });
                const canvas = document.getElementById('pdf-canvas');
                const context = canvas.getContext('2d');

                canvas.width = viewport.width;
                canvas.height = viewport.height;

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                page.render(renderContext).promise.then(function() {
                    //console.log("PDF rendered.");
                    generateQRCode();
                });
            });
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
        });
    }

    function generateQRCode() {
        const wrapper = $('#qr-code-wrapper');
        const code = wrapper.data('code') || "{{ $code }}";
        wrapper.html('');

        // Buat inner div tempat canvas QR Code
        const inner = $('<div id="qr-inner" style="width: 100%; height: 100%;"></div>');
        wrapper.append(inner);

        // Fungsi untuk membuat QR Code sesuai ukuran
        function renderQRCode(width) {
            inner.html(''); // Hapus QR lama
            new QRCode(inner[0], {
                text: code,
                width: width,
                height: width,
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        // Render awal
        const initialWidth = wrapper.width();
        renderQRCode(initialWidth);

        // Delay agar canvas siap
        setTimeout(() => {
            wrapper.draggable({
                containment: '#pdf-container',
                stop: function(event, ui) {
                    wrapper.attr('data-x', ui.position.left);
                    wrapper.attr('data-y', ui.position.top);
                }
            });

            wrapper.resizable({
                aspectRatio: 1,
                containment: '#pdf-container',
                handles: 'n, e, s, w, ne, se, sw, nw',
                resize: function(event, ui) {
                    const newSize = ui.size.width;
                    renderQRCode(newSize); // regenerate QR dengan ukuran baru
                }
            });
        }, 100);
    }



    function savePDF() {
        const canvas = document.getElementById('pdf-canvas');

        const qrCanvas = document.querySelector('#qr-code-wrapper canvas');
        const qrWrapper = document.getElementById('qr-code-wrapper');

        const pdf = new jspdf.jsPDF({
            orientation: 'portrait',
            unit: 'px',
            format: [canvas.width, canvas.height]
        });

        const ctx = canvas.getContext('2d');
        const canvasWithQR = document.createElement('canvas');
        canvasWithQR.width = canvas.width;
        canvasWithQR.height = canvas.height;
        const ctxWithQR = canvasWithQR.getContext('2d');

        // Gambar isi PDF asli
        ctxWithQR.drawImage(canvas, 0, 0);

        // Ambil posisi dan ukuran QR Code
        const qrX = parseInt(qrWrapper.getAttribute('data-x')) || 10;
        const qrY = parseInt(qrWrapper.getAttribute('data-y')) || 10;
        const qrWidth = qrWrapper.offsetWidth;
        const qrHeight = qrWrapper.offsetHeight;

        // Gambar QR Code dengan ukuran yang benar
        ctxWithQR.drawImage(qrCanvas, qrX, qrY, qrWidth, qrHeight);

        // Convert ke image
        const finalImage = canvasWithQR.toDataURL('image/jpeg', 1.0);

        // Masukkan ke PDF
        pdf.addImage(finalImage, 'JPEG', 0, 0, canvas.width, canvas.height);

        // Kirim ke server
        const blob = pdf.output('blob');
        const formData = new FormData();
        formData.append('file', blob, 'output.pdf');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', '{{ $code }}');
        formData.append('document_id', '{{ $document_id }}');

        $.ajax({
            url: '{{ route('savePdfToServer') }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    html: response.message,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        closeModal();
                        $('#table_data').DataTable().ajax.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    html: xhr.responseJSON?.message || "Terjadi kesalahan",
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                });
            }
        });
    }



    $('#form_data').on('submit', function(e) {
        e.preventDefault();
        savePDF();
    });
</script>
