var carousels = document.querySelectorAll('.carousel');

for (var j = 0; j < carousels.length; j++) {
  (function () {
    var carousel = carousels[j];
    var images = carousel.getElementsByTagName('img');
    var currentIndex = 0;

    function showImage(index) {
      for (var i = 0; i < images.length; i++) {
        images[i].style.display = i === index ? 'block' : 'none';
      }
    }

    function nextImage() {
      currentIndex = (currentIndex + 1) % images.length;
      showImage(currentIndex);
    }

    function prevImage() {
      currentIndex = (currentIndex - 1 + images.length) % images.length;
      showImage(currentIndex);
    }

    showImage(currentIndex);

    var prevButton = document.createElement('div');
    prevButton.className = 'prev';
    prevButton.innerHTML = '&#10094;';
    prevButton.addEventListener('click', prevImage);
    carousel.appendChild(prevButton);

    var nextButton = document.createElement('div');
    nextButton.className = 'next';
    nextButton.innerHTML = '&#10095;';
    nextButton.addEventListener('click', nextImage);
    carousel.appendChild(nextButton);
  })();
}
