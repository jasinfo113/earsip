<div id="overlay"></div>
<form id="form_data" method="post" privilege="form" autocomplete="off" onsubmit="return false;">
    @csrf
    <input type="hidden" name="save" value="true" />
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">{{ $title }}</h2>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" onclick="closeModal()">
                    <i class="fa fa-times fs-3"></i>
                </button>
            </div>
            <div class="modal-body m-4">
                <div class="d-flex flex-column m-3">
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2" id="input_role">Role</label>
                        <select class="form-select form-select-solid mb-3 mb-lg-0" name="role_id" id="input_role" data-control="select2" data-placeholder="Select an option" required>
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="fv-row">
                        <label class="required fw-bold fs-6 mb-2" id="input_menu">Menus</label>
                        <div class="form-check form-check-custom form-check-lg form-check-solid justify-content-start mb-3">
                            <input type="checkbox" class="form-check-input" id="menuAll" value="1" />
                            <label class="form-check-label fs-6" for="menuAll">
                                Select All
                            </label>
                        </div>
                        <select class="multi-select" name="menu_ids[]" id="input_menu" multiple required>
                        </select>
                    </div>
                    <div class="separator separator-dashed border-gray my-10"></div>
                    <div class="d-flex flex-column align-items-start justify-content-start gap-4 mb-7">
                        <div class="form-check form-check-custom form-check-solid form-check-lg justify-content-start">
                            <input type="checkbox" class="form-check-input" name="create" id="input_create" value="1" />
                            <label class="form-check-label fs-6" for="input_create">
                                Create
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid form-check-lg justify-content-start">
                            <input type="checkbox" class="form-check-input" name="update" id="input_update" value="1" />
                            <label class="form-check-label fs-6" for="input_update">
                                Update
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid form-check-lg justify-content-start">
                            <input type="checkbox" class="form-check-input" name="delete" id="input_delete" value="1" />
                            <label class="form-check-label fs-6" for="input_delete">
                                Detele
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid form-check-lg justify-content-start">
                            <input type="checkbox" class="form-check-input" name="export" id="input_export" value="1" />
                            <label class="form-check-label fs-6" for="input_export">
                                Export
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid form-check-lg justify-content-start">
                            <input type="checkbox" class="form-check-input" name="approve" id="input_approve" value="1" />
                            <label class="form-check-label fs-6" for="input_approve">
                                Approve
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-end gap-2">
                <button type="button" class="btn btn-light btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-primary btn-submit">
                    <span class="indicator-label">Submit</span>
                    <span class="indicator-progress">Please wait...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function () {
        setSelect2("#form_data select[data-control=select2]");
        setItems("#form_data select[name=role_id]", roleItems);
        setItems("#form_data select[id=input_menu]", menuItems);
        $("#form_data select[id=input_menu]").multiSelect({
            selectableOptgroup	: true,
            selectableHeader	: '<input type="text" class="form-control search-input optional" autocomplete="off" placeholder="Search . . ." />',
            selectionHeader		: '<input type="text" class="form-control search-input optional" autocomplete="off" placeholder="Search . . ." />',
            afterInit			: function(ms){
                var that 	= this,
                    $selectableSearch 		= that.$selectableUl.prev(),
                    $selectionSearch 		= that.$selectionUl.prev(),
                    selectableSearchString 	= '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString 	= '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString,{
                    'show': function () {
                        $(this).prev(".ms-optgroup-label").show();
                        $(this).show();
                    },
                    'hide': function () {
                        $(this).prev(".ms-optgroup-label").hide();
                        $(this).hide();
                    }
                }).on('keydown', function(e){
                    if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                    }
                });
                that.qs2 = $selectionSearch.quicksearch(selectionSearchString,{
                    'show': function () {
                        $(this).prev(".ms-optgroup-label").show();
                        $(this).show();
                    },
                    'hide': function () {
                        $(this).prev(".ms-optgroup-label").hide();
                        $(this).hide();
                    }
                }).on('keydown', function(e){
                    if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                    }
                });
            },
            afterSelect			: function(){
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect		: function(){
                this.qs1.cache();
                this.qs2.cache();
            }
        });
        $("#form_data #menuAll").click(function(){
            if ($(this).is(':checked')) {
                $("#form_data #input_menu").multiSelect("select_all");
            } else {
                $("#form_data #input_menu").multiSelect("deselect_all");
            }
        });
        $("#form_data .btn-submit").click(function (e) {
            e.preventDefault();
            saveFormData("#form_data", "manage/user/privilege/save", showData, true, true);
        });
        $("#form_data input").keydown(function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode === 13) {
                e.preventDefault();
                if (_pass.getScore() >= _passMin) {
                    $("#form_data .btn-submit").trigger("click");
                }
            }
        });
    });
</script>