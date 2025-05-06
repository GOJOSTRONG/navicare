document.addEventListener('DOMContentLoaded', function () {
  console.log('DOM fully loaded');

  // Get elements
  const menu = document.getElementById('accessibilityMenu');
  const contentDisplay = document.getElementById('accessibilityContent');
  const overlay = document.querySelector('.accessibility-overlay');
  const closeButton = document.getElementById('closeAccessibilityContent');

  if (!menu) console.error('Accessibility menu not found');
  if (!contentDisplay) console.error('Content display not found');
  if (!overlay) console.error('Overlay not found');
  if (!closeButton) console.error('Close button not found');

      // If content is visible, hide it
      if (contentDisplay.classList.contains('active')) {
        contentDisplay.classList.remove('active');
        overlay.classList.remove('active');
      }

  // Menu buttons functionality
  const menuButtons = document.querySelectorAll('#accessibilityMenu button');
  menuButtons.forEach(button => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      const contentId = this.dataset.content; // Make sure this is the correct attribute (like 'aboutUs')
      console.log('Menu button clicked:', contentId);

      if (contentId) {
        showAccessibilityContent(contentId);
      } else {
        console.error('No content ID found on button:', this);
      }
    });
  });

  // Close button functionality
  if (closeButton) {
    closeButton.addEventListener('click', () => {
      contentDisplay.classList.remove('active');
      overlay.classList.remove('active');
    });
  }

  // Show content and apply blur
  function showAccessibilityContent(contentId) {
    console.log('Show content called for:', contentId);

    // Define the content
    const accessibilityContent = {
      aboutUs: {
        title: "ABOUT US",
        description: "Motherbords and Babybytes is a dedicated team of Level 2 students from the West Visayas State University College of Nursing. As part of our advocacy to bridge healthcare and technology, we developed NaviCare—a one-stop platform for learning, hospital navigation, and health support. NaviCare brings together nursing education, hospital services, and postpartum care in one accessible and user-friendly space."
      },
      wvsuVision: {
        title: "WVSU VISION",
        description: "A research university advancing quality education towards societal transformation and global recognition."
      },
      wvsuMission: {
        title: "WVSU MISSION",
        description: "WVSU commits to develop life-long learners empowered to generate knowledge and technology, and transform communities as agents of change."
      }
    };

    // Fetch content based on ID
    const content = accessibilityContent[contentId];

    if (!content) {
      console.error('Content not found for ID:', contentId);
      return;
    }

    // Update the content display with the fetched content
    const titleEl = document.getElementById('contentTitle');
    const descEl = document.getElementById('contentDescription');

    if (!titleEl || !descEl) {
      console.error('Content elements not found');
      return;
    }

    titleEl.textContent = content.title;
    descEl.textContent = content.description;

    // Show the content and overlay
    contentDisplay.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent scrolling when content is open
  }
});

  //Home button
  const backButton = document.querySelector('.back-button');
  if (backButton) {
    backButton.addEventListener('click', function(e) {
      e.preventDefault();
      console.log('Back button clicked');
      backtoHome(); // 🚀 this does the redirect
    });
  }  
// Back to log in button
  document.addEventListener('DOMContentLoaded', function () {
    const backButton2 = document.querySelector('.back-button2');
  
    if (backButton2) {
      backButton2.addEventListener('click', function (e) {
        e.preventDefault(); // optional — only useful if inside a form
        console.log('Back to login clicked');
        window.location.href = 'login_student.html';
      });
    }
  });


function backtoHome() {
  window.location.href = 'index.html';
}
function backToLogIn() {
  window.location.href = 'login_student.html';
}
//Case sensitive function
document.querySelector("form").addEventListener("submit", function(e) {
  const patterns = {
    fullname: /^[A-Za-z\s]{2,50}$/,
    username: /^[a-zA-Z0-9._-]{4,20}$/,
    idnumber: /^\d{6,10}$/,
    email: /^[\w.-]+@[a-zA-Z\d.-]+\.[a-zA-Z]{2,}$/,
    dob: /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{2}$/,
    password: /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/
  };

  const form = e.target;
  let isValid = true;

  for (let field in patterns) {
    const input = form[field];
    if (!patterns[field].test(input.value)) {
      alert(`Please enter a valid ${field.replace(/([A-Z])/g, ' $1')}.`);
      isValid = false;
      break;
    }
  }

  if (!isValid) e.preventDefault();
});