<main class="container py-5" style="margin-top: 20px;">
    <div class="row">
        <div class="text-center">
            <h3>List Export</h3>
        </div>
        <div class="d-flex justify-content-center">
            <div>
                <form id="upload-file" method="post" enctype="multipart/form-data">
                </form>
                <button type="button" id="triggerUpload" class="btn btn-primary">
                    <input type="file" id="uploadExcelInput" name="excel_file" accept=".xlsx, .xls, .csv" onchange="importExcel()" form="upload-file" hidden>
                    <input type="submit" form="upload-file" hidden>
                    <i class="bi bi-file-earmark-arrow-up pe-1"></i>
                    Import Excel
                </button>
                <a href="<?= base_url(); ?>Booking/download_template" target="_blank" class="btn btn-success">
                    <i class="bi bi-file-earmark-arrow-down pe-1"></i>
                    Template
                </a>
                <button type="button" class="btn btn-warning text-white" id="btnGetData">
                    <i class="bi bi-file-earmark-text"></i>
                    Get Data
                </button>
                <button type="button" id="btnSubmit" class="btn btn-primary" style="display: none;">
                    <i class="bi bi-send"></i>
                    Submit
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="pt-2">
            <table class="table table-responsive table-striped tabled-hover rounded-2" id="table-import">
                <thead class="text-center">
                    <tr>
                        <th style="background-color: #060771;color: white;">No</th>
                        <th style="background-color: #060771;color: white;">Kode Pembelajaran</th>
                        <th style="background-color: #060771;color: white;">Judul Pembelajaran</th>
                        <th style="background-color: #060771;color: white;">Start Date</th>
                        <th style="background-color: #060771;color: white;">End Date</th>
                        <th style="background-color: #060771;color: white;">Durasi</th>
                        <th style="background-color: #060771;color: white;">NIP</th>
                        <th style="background-color: #060771;color: white;">Nama</th>
                        <th style="background-color: #060771;color: white;">Jabatan</th>
                        <th style="background-color: #060771;color: white;">Unit Induk</th>
                        <th style="background-color: #060771;color: white;">Jenis Kelamin</th>
                        <th style="background-color: #e67e22;color: white;">Ruangan</th>
                        <th style="background-color: #e67e22;color: white;">Kamar</th>
                    </tr>
                </thead>
                <tbody id="body-table"></tbody>
            </table>
        </div>
    </div>

</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const triggerButton = document.getElementById('triggerUpload');
        const uploadInput = document.getElementById('uploadExcelInput');
        const fileNameDisplay = document.getElementById('fileNameDisplay');

        if (triggerButton && uploadInput) {
            triggerButton.addEventListener('click', function() {
                uploadInput.click();
            });
        }

        if (uploadInput && fileNameDisplay) {
            uploadInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    fileNameDisplay.textContent = this.files[0].name;
                } else {
                    fileNameDisplay.textContent = 'Tidak ada file yang dipilih';
                }
            });
        }
        getFloorAvail()
    });
    var room = "";
    var meet = "";

    function getFloorAvail() {
        $.ajax({
            url: '<?= base_url() ?>Booking/getFloor',
            type: 'POST',
            data: {
                tanggal: 'sekarang',
            },
            success: (response) => {
                var res = JSON.parse(response)
                room = "";
                meet = "";
                if (res.msg['room'].length > 0) {
                    $.each(res.msg['room'], (key, val) => {
                        room += `<option value="` + val.id + `">` + val.room_number + `</option>`
                    })
                }
                if (res.msg['meet'].length > 0) {
                    $.each(res.msg['meet'], (key, val) => {
                        meet += `<option value="` + val.id + `">` + val.room_name + `</option>`
                    })
                }
            },
            error: (ee) => {
                var res = JSON.parse(ee.responseText)

            }
        })
    }

    function importExcel() {
        submitimportExcel()
        $('#uploadExcelInput').val('')
    }

    function submitimportExcel() {
        var f = new FormData(document.getElementById('upload-file'))
        $.ajax({
            url: '<?= base_url() ?>Booking/impBatch',
            type: 'POST',
            data: f,
            contentType: false,
            cache: false,
            processData: false,
            success: (response) => {
                var res = JSON.parse(response)
                loopData(res.msg.data, res.msg.status)
            },
            error: (ee) => {
                var res = JSON.parse(ee.responseText)
                toastr.error(res.msg.status)
            }
        })
    }

    $('#btnSubmit').on('click', (e) => {
        e.preventDefault()
        // var table = $('#table-import')
        var table = document.getElementById('table-import')
        var row = table.querySelectorAll('tbody tr')
        var data = []
        var flag = 0;
        $.each(row, (key, val) => {
            var row2 = val.querySelectorAll("td")
            var row_data = {}
            var columnKeys = [
                'No', 'Kode_Pembelajaran', 'Judul_Pembelajaran', 'Start_Date',
                'End_Date', 'Durasi', 'NIP', 'Nama', 'Jabatan', 'Unit_Induk', 'Jenis_Kelamin', 'Ruangan', 'Kamar'
            ];

            if (flag == 0) {
                $.each(row2, (key2, val2) => {
                    if (flag == 0) {
                        if (val2.textContent.trim() == "null") {
                            flag = 1
                            return;
                        }

                        if (columnKeys[key2] == "Ruangan" || columnKeys[key2] == "Kamar") {
                            var selected = $(val2).find('select').val();
                            row_data[columnKeys[key2]] = selected;
                        } else {
                            row_data[columnKeys[key2]] = val2.textContent.trim();
                        }
                    } else {
                        return;
                    }
                })
                data.push(row_data)
            } else {
                return;
            }
        })
        if (flag == 1) {
            return toastr.warning("terdapat data yang kurang lengkap, mohon untuk melengkapi data")
        } else {
            $.ajax({
                url: '<?= base_url() ?>Booking/addBatchBook',
                type: 'POST',
                data: {
                    data: data,
                },
                success: (response) => {
                    var res = JSON.parse(response)
                    toastr.success(res.msg)
                    $('#body-table').empty()
                    $('#btnSubmit').hide()
                },
                error: (ee) => {
                    var res = JSON.parse(ee.responseText)
                    toastr.error(res.msg)
                }
            })
        }
    })

    $('#btnGetData').on("click", (e) => {
        e.preventDefault()
        $.ajax({
            url: '<?= base_url() ?>Booking/getDataBatch',
            type: 'POST',
            success: (response) => {
                var res = JSON.parse(response)
                loopData(res, "berhasil")
            },
            error: (ee) => {
                var res = JSON.parse(ee.responseText)
                toastr.error(res)
                console.log(res);
            }
        })
    })

    function loopData(data, status) {
        var markup = '';
        $('#body-table').empty()
        if (data.length > 0) {
            $('#btnSubmit').show()
            $.each(data, (key, val) => {
                markup += `
                        <tr>
                            <td>` + val.no + `</td>
                            <td>` + val.kode_pembelajaran + `</td>
                            <td>` + val.judul_pembelajaran + `</td>
                            <td>` + val.start_date + `</td>
                            <td>` + val.end_date + `</td>
                            <td>` + val.durasi + `</td>
                            <td>` + val.nip + `</td>
                            <td>` + val.nama + `</td>
                            <td>` + val.jabatan + `</td>
                            <td>` + val.unit_induk + `</td>
                            <td>` + val.jenis_kelamin + `</td>
                            <td>
                                <select class="select2" style="width:100%">` + meet + `</select>
                            </td>
                            <td>
                                <select class="select2" style="width:100%">` + room + `</select>
                            </td>
                        </tr>`
            })
            toastr.success(status)
        } else {
            $('#btnSubmit').hide()
            markup += `
            <tr>
            <td>Belum Ada Data</td>
            </tr>`
        }
        $('#body-table').append(markup)
        $('.select2').select2()
    }
</script>