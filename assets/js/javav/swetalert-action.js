document.addEventListener("DOMContentLoaded", () => {

  function confirmAction(selector, title, text, icon, confirmText) {
    document.querySelectorAll(selector).forEach(btn => {
      btn.addEventListener("click", function (e) {
        e.preventDefault();

        Swal.fire({
          title: title,
          text: text,
          icon: icon,
          showCancelButton: true,
          confirmButtonText: confirmText,
          cancelButtonText: "Batal"
        }).then(result => {
          if (result.isConfirmed) {
            window.location.href = btn.href;
          }
        });
      });
    });
  }

  confirmAction('.btn-keluar','Yakin Keluar?','Anda akan Keluar dari aplikasi.','warning','Ya, keluar!');
  confirmAction('.btn-activate','Cairkan Pinjaman?','Dana akan dicairkan dan status berubah menjadi aktif.','question','Ya, cairkan!');
  confirmAction('.btn-closed','Tutup Pinjaman?','Pinjaman akan ditutup.','info','Ya, tutup!');
  confirmAction('.btn-veri','Yakin Verifikasi?','Data akan disetujui.','warning','Ya, setujui!');
  confirmAction('.btn-verif','Yakin Verifikasi?','Data akan disetujui.','warning','Ya, setujui!');

});