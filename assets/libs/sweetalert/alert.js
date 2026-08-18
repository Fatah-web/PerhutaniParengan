const icon = $(".flash-data").data("status");
const text = $(".flash-data").data("text");
const title = $(".flash-data").data("title");

if (icon) {
  Swal.fire({
    icon: icon,
    title: title,
    text: text,
  }).then((result) => {
    // After the alert closes, make an AJAX request to unset the session
    $.ajax({
      url: "../assets/libs/sweetalert/unset_alert.php", // URL of the PHP file that will unset the session
      type: "GET", // or 'POST' depending on your preference
      success: function (response) {
        console.log("Session unset successfully.");
      },
      error: function (xhr, status, error) {
        console.log("Error unsetting session: " + error);
      },
    });
  });
}

$(".btn-hapus").on("click", function (e) {
  e.preventDefault(); // Prevent the default form submission

  const href = $(this).attr("href");

  Swal.fire({
    icon: "warning",
    title: "Konfirmasi Hapus",
    text: "Apakah Anda yakin ingin menghapus data ini?",
    showCancelButton: true,
    confirmButtonText: "Hapus",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      // If confirmed, redirect to the href
      window.location.href = href;
    }
  });
});
