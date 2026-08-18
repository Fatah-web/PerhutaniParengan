function openImage(src) {
  document.getElementById("lightbox-img").src = src;
  document.getElementById("lightbox").classList.add("show");
}

function closeImage() {
  document.getElementById("lightbox").classList.remove("show");
}