// Content data for accessibility sections
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

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM fully loaded');
  
  // Get elements
  const menu = document.getElementById('accessibilityMenu');
  const contentDisplay = document.getElementById('accessibilityContent');
  const overlay = document.querySelector('.accessibility-overlay');
  const accessButton = document.querySelector('.accessibility-button');
  
  // Check if elements exist
  if (!menu) console.error('Accessibility menu not found');
  if (!contentDisplay) console.error('Content display not found');
  if (!overlay) console.error('Overlay not found');
  if (!accessButton) console.error('Accessibility button not found');
  
  // Add click event to accessibility button
  if (accessButton) {
    accessButton.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Accessibility button clicked');
      toggleAccessibilityMenu();
    });
  }
  
  // Add click events to menu buttons - FIXED THIS PART
  const menuButtons = document.querySelectorAll('#accessibilityMenu button');
  menuButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      // Get the contentId directly from data attribute instead of parsing onclick
      const contentId = this.dataset.content;
      console.log('Menu button clicked:', contentId);
      
      if (contentId) {
        showAccessibilityContent(contentId);
      } else {
        console.error('No content ID found on button:', this);
      }
    });
  });
  
  // Add click event to back button
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
  
  // Click outside to close
  document.addEventListener('click', function(event) {
    const button = document.querySelector('.accessibility-button');
    
    if (
      (menu.classList.contains('active') || contentDisplay.classList.contains('active')) &&
      !menu.contains(event.target) &&
      !contentDisplay.contains(event.target) &&
      !button.contains(event.target)
    ) {
      console.log('Clicked outside, closing menus');
      menu.classList.remove('active');
      contentDisplay.classList.remove('active');
      overlay.classList.remove('active');
      document.body.style.overflow = 'auto';
    }
  });
});

// Toggle menu and blur
function toggleAccessibilityMenu() {
  console.log('Toggle accessibility menu called');
  const menu = document.getElementById('accessibilityMenu');
  const contentDisplay = document.getElementById('accessibilityContent');
  const overlay = document.querySelector('.accessibility-overlay');
  
  if (!menu || !contentDisplay || !overlay) {
    console.error('Required elements not found');
    return;
  }
  
  const isActive = menu.classList.contains('active');
  console.log('Menu is active:', isActive);
  
  if (isActive) {
    // Hide everything
    menu.classList.remove('active');
    contentDisplay.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
  } else {
    // Show menu 
    menu.classList.add('active');
    contentDisplay.classList.remove('active'); // Just in case
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

// Show content
function showAccessibilityContent(contentId) {
  console.log('Show content called for:', contentId);
  const content = accessibilityContent[contentId];
  if (!content) {
    console.error('Content not found for ID:', contentId);
    return;
  }
  
  const menu = document.getElementById('accessibilityMenu');
  const contentDisplay = document.getElementById('accessibilityContent');
  const titleEl = document.getElementById('contentTitle');
  const descEl = document.getElementById('contentDescription');
  
  if (!menu || !contentDisplay || !titleEl || !descEl) {
    console.error('Required elements not found');
    return;
  }
  
  titleEl.textContent = content.title;
  descEl.textContent = content.description;
  
  menu.classList.remove('active');
  contentDisplay.classList.add('active');
}

// Back to menu
function backToAccessibilityMenu() {
  console.log('Back to menu called');
  const menu = document.getElementById('accessibilityMenu');
  const contentDisplay = document.getElementById('accessibilityContent');
  
  if (!menu || !contentDisplay) {
    console.error('Required elements not found');
    return;
  }
  
  contentDisplay.classList.remove('active');
  menu.classList.add('active');
}
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