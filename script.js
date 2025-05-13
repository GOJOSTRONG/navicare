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
        idnumber: /^\d{6,10}$/, // Example: 6-10 digits. PHP also validates this.
        // Updated email regex to require @wvsu.edu.ph (case-insensitive for the domain part)
        email: /^[a-zA-Z0-9._%+-]+@wvsu\.edu\.ph$/i,
        // Updated DOB regex for MM/DD/YYYY format
        dob: /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/(19\d\d|20\d\d)$/, // Allows years 1900-2099
        password: /^[^\s]+$/ // Checks for no whitespace
    };

    const values = {
        fullname: form.fullname.value.trim(),
        username: form.username.value.trim(),
        idnumber: form.idnumber.value.trim(),
        email: form.email.value.trim(),      // Original case is preserved
        dob: form.dob.value.trim(),
        password: form.password.value        // No trim, matches PHP
    };

    // Check for empty fields first (mimicking PHP's initial check)
    // All fields in 'values' are expected by the PHP 'isset' and 'empty' checks
    for (const key in values) {
        if (values.hasOwnProperty(key)) {
            // For password, empty check is implicit in length check later.
            // For other fields, if they are empty, the regex test will likely fail.
            // However, an explicit empty check can give a clearer message.
            if (!values[key]) {
                let fieldName = key.replace(/([A-Z])/g, " $1");
                fieldName = fieldName.charAt(0).toUpperCase() + fieldName.slice(1);
                alert(`${fieldName} is required.`);
                e.preventDefault();
                return;
            }
        }
    }


    for (let key in patterns) {
        if (!patterns[key].test(values[key])) {
            let fieldName = key.replace(/([A-Z])/g, " $1");
            fieldName = fieldName.charAt(0).toUpperCase() + fieldName.slice(1);
            let errorMessage = `Invalid ${fieldName}.`;

            if (key === 'email') {
                errorMessage = 'Invalid Email. Must be a valid format ending with @wvsu.edu.ph.';
            } else if (key === 'dob') {
                errorMessage = 'Invalid Date of Birth. Please use MM/DD/YYYY format (e.g., 01/23/1990).';
            }
            // For other fields, the generic "Invalid [FieldName]." message will be used.

            alert(errorMessage);
            e.preventDefault();
            return;
        }
    }

    // --- Additional specific validations ---

    // 1. Date of Birth - check actual date validity (e.g., not 02/30/2000)
    // This runs if the MM/DD/YYYY format regex passed.
    const dobValue = values.dob;
    const dobParts = dobValue.split('/');
    const month = parseInt(dobParts[0], 10);
    const day = parseInt(dobParts[1], 10);
    const year = parseInt(dobParts[2], 10);
    const dateObj = new Date(year, month - 1, day); // JavaScript month is 0-indexed

    if (!(dateObj.getFullYear() === year && dateObj.getMonth() === (month - 1) && dateObj.getDate() === day)) {
        alert('Invalid Date of Birth. Please ensure it is a valid calendar date (e.g., not 02/30/2000).');
        e.preventDefault();
        return;
    }

    // 2. Password length (PHP checks for < 8 as an example)
    if (values.password.length < 8) {
        alert('Password must be at least 8 characters long.');
        e.preventDefault();
        return;
    }

    // If all client-side validations pass, the form will submit
    // because e.preventDefault() is only called if an error is found.
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
    