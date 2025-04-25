<div id="overlay"></div>
<form id="form_data" method="post" role="form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
    @csrf
    @if (isset($row->id))
        <input type="hidden" name="update" value="true" />
        <input type="hidden" name="id" value="{{ $row->id }}" />
    @else
        <input type="hidden" name="save" value="true" />
    @endif

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
                <div id="qr-code" data-x="10" data-y="10"></div>
                <input type="text" name="update" id="namafile" value="{{ $nama_file }}" />
                <input type="text" name="id" value="{{ $code }}" />
                <div class="modal-footer flex-end gap-2">
                    <button type="button" class="btn btn-light btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit">
                        <span class="indicator-label">Submit</span>
                        <span class="indicator-progress">Please wait...<span
                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
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

    #qr-code {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 100px;
        height: 100px;
        background: transparent;
        cursor: grab;
        z-index: 10;
    }
</style>

<script>
    function loadPDF() {
        const pdfUrl = "{{ $nama_file }}"; // URL file PDF
        const loadingTask = pdfjsLib.getDocument(pdfUrl);

        loadingTask.promise.then(function(pdf) {
            pdf.getPage(1).then(function(page) {
                const scale = 1.5;
                const viewport = page.getViewport({ scale });
                const canvas = document.getElementById('pdf-canvas');
                const context = canvas.getContext('2d');

                canvas.width = viewport.width;
                canvas.height = viewport.height;

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                page.render(renderContext).promise.then(function() {
                    console.log("PDF rendered.");
                    generateQRCode();
                });
            });
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
        });
    }
    function generateQRCode() {
    let qrContainer = $('#qr-code');
    qrContainer.html('');

    qrCode = new QRCode(qrContainer[0], {
        text: "{{ $code }}",
        width: 90,
        height: 90,
        correctLevel: QRCode.CorrectLevel.H
    });

    setTimeout(() => {
        qrContainer.find('canvas').css('background', 'transparent');
        qrContainer.draggable({
            containment: '#pdf-container',
            stop: function (event, ui) {
                // Simpan posisi terakhir ke atribut data
                $(this).attr('data-x', ui.position.left);
                $(this).attr('data-y', ui.position.top);
            }
        });
    }, 100); // kasih jeda supaya canvas sudah siap
}


</script>
