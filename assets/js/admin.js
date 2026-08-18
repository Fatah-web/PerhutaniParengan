/* ================================
   ADMIN.JS (Gabungan Semua Script)
   ================================ */

// ===================================
// 1. DATATABLE + FILTER + SELECT2
// ===================================
$(document).ready(function () {

    // Inisialisasi DataTable
    var table = $('#example').DataTable({
        language: {
            decimal: ",",
            thousands: ".",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(disaring dari _MAX_ data)",
            zeroRecords: "Tidak ditemukan data yang cocok",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "›",
                previous: "‹"
            }
        }
    });

    // Select2 di modal
    $('.selectinput').each(function () {
        $(this).select2({
            theme: 'custom',
            dropdownParent: $(this).closest('.modal'),
            width: '100%'
        });
    });

    // Select2 di luar modal
    $('.selectp').select2({
        theme: 'custom',
        dropdownParent: $('body'),
        width: '100%'
    });

    // Filter tabel
    $('#filterType').on('change', function () {
        table.column(2).search(this.value).draw();
        hitungTotalTampil();
    });

    $('#filternasabah').on('change', function () {
        table.column(1).search(this.value).draw();
        hitungTotalTampil();
    });

    $('#filterJenis').on('change', function () {
        table.column(2).search(this.value).draw();
        hitungTotalTampil();
    });

    $('#filterKategori').on('change', function () {
        table.column(3).search(this.value).draw();
        hitungTotalTampil();
    });
    // Filter Status
    $('#filter_transfer').on('change', function () {
        table.column(7).search(this.value).draw();
    });

    // Filter Status (custom)
    $('#filter_status').on('change', function () {
        var value = $(this).val().toLowerCase();

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            var statusCell = $(table.row(dataIndex).node())
                .find('td:eq(8) button')
                .text()
                .toLowerCase()
                .trim();

            return !value || statusCell === value;
        });

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    // Hitung total tabel setelah filter
    function hitungTotalTampil() {
        var total = 0;

        table.rows({ filter: 'applied' }).every(function () {
            var data = this.data();
            var tipe = data[2].trim().toLowerCase();
            var jumlah = parseFloat(data[4].replace(/\./g, '').replace(',', '.')) || 0;

            if (tipe.includes("debit")) total += jumlah;
            else if (tipe.includes("kredit")) total -= jumlah;
        });

        const totalFormat = total.toLocaleString('id-ID', { minimumFractionDigits: 2 });
        let warna = total >= 0 ? 'text-success' : 'text-danger';

        $('#totalHasilFilter').html(
            `Total hasil pencarian: <b class="${warna}">Rp ${totalFormat}</b>`
        );
    }

    table.on('draw', hitungTotalTampil);
    hitungTotalTampil();

});



// ===============================
// VERIFIKASI
// ===============================
$(document).on('click', '.btn-verifikasi', function () {
    let id = $(this).data('id');
    let url = $(this).data('url');

    $.post(url, { transfer_id: id }, function (res) {
        Swal.fire("Berhasil!", "Transfer berhasil diverifikasi!", "success")
            .then(() => location.reload());
    });
});


// ===============================
// TOLAK → BUKA MODAL
// ===============================
$(document).on('click', '.btn-tolak', function () {
    $('#reject_id').val($(this).data('id'));
    $('#reject_url').val($(this).data('url'));
    $('#modalTolak').modal('show');
});


// ===============================
// SUBMIT TOLAK
// ===============================
$(document).on('submit', '#formReject', function (e) {
    e.preventDefault();

    let id = $('#reject_id').val();
    let url = $('#reject_url').val();
    let reason = $('textarea[name="reason"]').val();

    $.post(url, { transfer_id: id, reason: reason }, function (res) {
        Swal.fire("Ditolak!", "Transfer berhasil ditolak!", "success")
            .then(() => location.reload());
    });
});
