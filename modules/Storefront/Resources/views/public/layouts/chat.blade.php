<style>
#chatIconContainer {
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 1000;
  }

  .chat-button {
    position: relative;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #E2387E;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }

  .chat-icon {
    width: 30px;
    height: 30px;
    fill: white;
    position: relative;
    z-index: 2;
  }

  /* Ripple Effects */
  .ripple {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: 2px solid #E2387E;
    border-radius: 50%;
    animation: ripple 2s infinite;
    opacity: 0;
  }

  .ripple:nth-child(2) {
    animation-delay: 0.5s;
  }

  .ripple:nth-child(3) {
    animation-delay: 1s;
  }

  @keyframes ripple {
    0% {
      transform: scale(1);
      opacity: 0;
    }
    50% {
      opacity: 0.3;
    }
    100% {
      transform: scale(1.5);
      opacity: 0;
    }
  }

  /* Wave Effect */
  .wave {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    z-index: 1;
  }

  /* Chat Options Panel */
  .chat-options {
    position: absolute;
    bottom: 80px;
    /*right: 0;*/
    background: white;
    border-radius: 16px;
    padding: 20px;
    width: 280px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
    pointer-events: none;
  }

  .chat-options.visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: all;
  }

  .chat-options h3 {
    margin: 0 0 10px;
    font-size: 18px;
    color: #333;
  }

  .chat-options p {
    margin: 0 0 20px;
    color: #666;
    font-size: 14px;
  }

  .chat-option {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    margin-bottom: 8px;
    border-radius: 8px;
    border: 1px solid #eee;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
  }

  .chat-option:hover {
    background-color: #f5f5f5;
    border-color: #ddd;
  }

  .chat-option-icon {
    width: 24px;
    height: 24px;
    margin-right: 12px;
    position: relative;
  }

  /* Messenger specific styling */
  .messenger-icon {
    position: relative;
    width: 100%;
    height: 100%;
  }

  .messenger-background {
    fill: url(#messenger-gradient);
  }

  .messenger-symbol {
    fill: white;
  }

  /* WhatsApp specific styling */
  .whatsapp-icon {
    position: relative;
    width: 100%;
    height: 100%;
  }

  .whatsapp-background {
    fill: #25D366;
  }

  .whatsapp-symbol {
    fill: white;
  }
  </style>


  <div id="chatIconContainer">
    <button class="chat-button">
      <div class="ripple"></div>
      <div class="ripple"></div>
      <div class="ripple"></div>
      <div class="wave"></div>
      <svg class="chat-icon" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 5.87 2 10.5c0 2.02 1.01 3.87 2.68 5.29-.09.56-.35 1.65-1.41 3.08-.15.21-.16.48-.04.71s.37.36.63.36c1.69 0 3.1-.43 4.26-1.02 1.37.41 2.89.58 4.43.58 5.52 0 10-3.87 10-8.5S17.52 2 12 2zm0 13c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
      </svg>
    </button>

    <div class="chat-options">
      <p>Hi There! 👋</p>
      <p>Let us know if we can help you with anything at all</p>

      <a href="https://m.me/276919608848358" target="_blank" class="chat-option">
        <div class="chat-option-icon">
          <svg class="messenger-icon" viewBox="0 0 36 36">
            <defs>
              <linearGradient id="messenger-gradient" x1="0%" y1="100%" x2="100%" y2="0%">
                <stop offset="0%" style="stop-color:#00B2FF" />
                <stop offset="100%" style="stop-color:#006AFF" />
              </linearGradient>
            </defs>
            <circle class="messenger-background" cx="18" cy="18" r="18" />
            <path class="messenger-symbol" d="M18 8.1c-5.5 0-10 4.2-10 9.4 0 3 1.5 5.6 3.8 7.4.2.2.3.4.3.7l.1 2.2c0 .3.3.5.6.4l2.5-1.1c.2-.1.4-.1.6 0 .9.2 1.8.4 2.8.4 5.5 0 10-4.2 10-9.4s-4.5-9.6-10-9.6zm.9 12.7l-1.9-2-3.8 2.1 4.2-4.5 1.9 2 3.8-2.1-4.2 4.5z"/>
          </svg>
        </div>
        Messenger
      </a>

      <a href="https://wa.me/+8801907888076" target="_blank" class="chat-option">
        <div class="chat-option-icon">
          <svg class="whatsapp-icon" viewBox="0 0 36 36">
            <circle class="whatsapp-background" cx="18" cy="18" r="18"/>
            <path class="whatsapp-symbol" d="M25.4 10.6C23.9 9 21.9 8 19.8 7.5c-2.1-.4-4.2-.3-6.2.4-2 .7-3.8 2-5.1 3.7-1.3 1.7-2 3.8-2 5.9 0 1.3.2 2.6.7 3.8L6 27.8l6.6-1.7c1.2.5 2.5.8 3.8.8h.1c1.1 0 2.1-.2 3.1-.5 1-.3 1.9-.8 2.7-1.4.8-.6 1.5-1.3 2.1-2.2.6-.8 1-1.8 1.3-2.8.3-1 .4-2 .3-3-.1-2.1-.8-4.1-2-5.8l.4-.6zm-5.8 13.2c-1 .6-2.1.9-3.3.9-1.2 0-2.4-.3-3.5-.8l-.2-.2-3.9 1 1-3.8-.2-.2c-.6-1.1-.9-2.3-.9-3.5 0-1.7.5-3.3 1.5-4.7 1-1.4 2.4-2.4 4-2.9 1.6-.5 3.3-.5 4.9 0s3 1.5 4 2.9c1 1.4 1.5 3 1.5 4.7 0 1.7-.5 3.3-1.5 4.7-1 1.3-2.4 2.3-4 2.9h.6z"/>
            <path class="whatsapp-symbol" d="M22.2 19.4c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.2-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.4.1-.6.1-.1.3-.3.4-.5.1-.2.2-.3.3-.5.1-.2 0-.4 0-.6-.1-.2-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.2 3c.1.2 1.9 3 4.7 4.1.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.2-1.3-.1-.2-.3-.3-.6-.4z"/>
          </svg>
        </div>
        WhatsApp
      </a>
    </div>
  </div>

  <script>
  // Wave animation
  function animateWave() {
    const wave = document.querySelector('.wave');
    let angle = 0;

    setInterval(() => {
      angle = (angle + 2) % 360;
      const x = 50 + Math.sin(angle * Math.PI / 180) * 50;
      const y = 50 + Math.cos(angle * Math.PI / 180) * 50;

      wave.style.mask = `radial-gradient(circle at ${x}% ${y}%, transparent 30%, black 70%)`;
      wave.style.webkitMask = `radial-gradient(circle at ${x}% ${y}%, transparent 30%, black 70%)`;
    }, 50);
  }

  // Toggle chat options
  const chatButton = document.querySelector('.chat-button');
  const chatOptions = document.querySelector('.chat-options');

  chatButton.addEventListener('click', (e) => {
    chatOptions.classList.toggle('visible');
    e.stopPropagation();
  });

  // Close chat options when clicking outside
  document.addEventListener('click', () => {
    chatOptions.classList.remove('visible');
  });

  // Prevent closing when clicking inside chat options
  chatOptions.addEventListener('click', (e) => {
    e.stopPropagation();
  });

  // Start wave animation when page loads
  document.addEventListener('DOMContentLoaded', animateWave);
  </script>
