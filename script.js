document.addEventListener('DOMContentLoaded', function () {
  console.log('DOM fully loaded');

  // Get elements
  const menu = document.getElementById('accessibilityMenu');
  const contentDisplay = document.getElementById('accessibilityContent');
  const overlay = document.querySelector('.accessibility-overlay');
  const closeButton = document.getElementById('closeAccessibilityContent');
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

document.addEventListener('DOMContentLoaded', function () {
  const backButton = document.querySelector('.back-button');
  if (backButton) {
    backButton.addEventListener('click', function (e) {
      e.preventDefault();
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.location.href = 'index.html'; // fallback if no history
      }
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const backButton2 = document.querySelector('.back-button2');

  if (backButton2) {
    backButton2.addEventListener('click', function (e) {
      e.preventDefault();
      window.location.href = 'index.html';
    });
  }
});

//Case sensitive function
document.getElementById("registerForm").addEventListener("submit", function (e) {
  const form = e.target;
  const patterns = {
    fullname: /^[A-Za-z\s]{2,50}$/,
    username: /^[a-zA-Z0-9._-]{4,20}$/,
    idnumber: /^\d{6,10}$/,
    email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    dob: /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{2}$/,
    password: /^[^\s]+$/
  };

  const values = {
    fullname: form.fullname.value.trim(),
    username: form.username.value.trim(),
    idnumber: form.idnumber.value.trim(),
    email: form.email.value.trim(),
    dob: form.dob.value.trim(),
    password: form.password.value
  };

  for (let key in patterns) {
    if (!patterns[key].test(values[key])) {
      alert(`Invalid ${key.replace(/([A-Z])/g, " $1")}.`);
      e.preventDefault();
      return;
    }
  }
});

//Terms and Conditions 
    // Modal functionality
    function openTermsModal() {
      document.getElementById('termsModal').style.display = 'block';
    }
    
    function closeTermsModal() {
      document.getElementById('termsModal').style.display = 'none';
    }
    
    function openPrivacyModal() {
      document.getElementById('privacyModal').style.display = 'block';
    }
    
    function closePrivacyModal() {
      document.getElementById('privacyModal').style.display = 'none';
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
      if (event.target == document.getElementById('termsModal')) {
        closeTermsModal();
      }
      if (event.target == document.getElementById('privacyModal')) {
        closePrivacyModal();
      }
    }
    
    function goBack() {
      window.location.href= 'register.html';
    }
    
    function proceedToLogin() {
      document.getElementById('termsModal').classList.remove('active');
      setTimeout(() => {
        window.location.href = 'login_student.html';
      }, 500); // waits half a second before redirect
    
      // Get URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      
      // Add each parameter as a hidden field
      const fields = ['fullname', 'username', 'idnumber', 'email', 'dob', 'password'];
      fields.forEach(field => {
        if (urlParams.has(field)) {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = field;
          input.value = urlParams.get(field);
          form.appendChild(input);
        }
      });
      
      // Append form to body and submit
      document.body.appendChild(form);
      form.submit();
    }
    