jQuery(document).ready(function($) {
  // Initialize slider with dots (pagination)
  $('.card-slider').slick({
    slidesToShow: 3,
    autoplay: true,
    slidesToScroll: 1,
    dots: true, // Enable dots/pagination
    arrows: true, // Show navigation arrows
    appendDots: $('.slider-dots-container'), // Custom dots container
    customPaging: function(slider, i) {
      return '<button class="slider-dot"></button>';
    },
    responsive: [
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          dots: true
        }
      }
    ]
  });

  // Modal functionality
  const cards = document.querySelectorAll('#blogslidercard .card');
  const modal = document.querySelector('.modal');
  const closeButton = document.querySelector('.modal__close-button');
  const page = document.querySelector('.page');
  const overlay = document.querySelector('.overlay');
  const animationDuration = 800; // Slow motion duration
  const timingFunction = 'cubic-bezier(0.4, 0, 0.2, 1)';

  let currentCard = null;
  let currentAnimation = null;

  // Show modal with disappearing card effect
  function showModal(card) {
    currentCard = card;
    $('.card-slider').slick('slickPause');
    page.dataset.modalState = 'opening';
    document.body.classList.add('no-scroll');

    // Card disappear animation
    const cardDisappear = card.animate([
      { transform: 'scale(1)', opacity: 1 },
      { transform: 'scale(0.5)', opacity: 0 }
    ], {
      duration: animationDuration/2,
      easing: timingFunction,
      fill: 'forwards'
    });

    cardDisappear.onfinish = () => {
      card.style.visibility = 'hidden';
      
      // Modal appear animation
      modal.style.display = 'block';
      overlay.style.display = 'block';
      modal.style.opacity = '0';
      modal.style.transform = 'scale(0.8)';
      
      currentAnimation = modal.animate([
        { transform: 'scale(0.8)', opacity: 0 },
        { transform: 'scale(1)', opacity: 1 }
      ], {
        duration: animationDuration,
        easing: timingFunction,
        fill: 'forwards'
      });

      currentAnimation.onfinish = () => {
        page.dataset.modalState = 'open';
      };
    };
  }

  // Hide modal with reverse animation
  function hideModal() {
    if (!currentCard) return;
    page.dataset.modalState = 'closing';
    
    // Modal disappear animation
    currentAnimation = modal.animate([
      { transform: 'scale(1)', opacity: 1 },
      { transform: 'scale(0.8)', opacity: 0 }
    ], {
      duration: animationDuration,
      easing: timingFunction,
      fill: 'forwards'
    });

    currentAnimation.onfinish = () => {
      modal.style.display = 'none';
      overlay.style.display = 'none';
      
      // Card reappear animation
      currentCard.style.visibility = 'visible';
      currentCard.style.opacity = '0';
      currentCard.style.transform = 'scale(0.5)';
      
      const cardReappear = currentCard.animate([
        { transform: 'scale(0.5)', opacity: 0 },
        { transform: 'scale(1)', opacity: 1 }
      ], {
        duration: animationDuration/2,
        easing: timingFunction,
        fill: 'forwards'
      });

      cardReappear.onfinish = () => {
        page.dataset.modalState = 'closed';
        document.body.classList.remove('no-scroll');
        $('.card-slider').slick('slickPlay');
        currentAnimation = null;
      };
    };
  }

  // Event listeners
  cards.forEach(card => {
    card.addEventListener('click', (e) => {
      if (currentAnimation) currentAnimation.cancel();
      showModal(e.currentTarget);
    });
  });

  closeButton.addEventListener('click', (e) => {
    e.stopPropagation();
    if (currentAnimation) currentAnimation.cancel();
    hideModal();
  });

  overlay.addEventListener('click', hideModal);
});