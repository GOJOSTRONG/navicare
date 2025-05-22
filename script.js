document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');


    // --- YOUR EXISTING BACK BUTTONS LOGIC ---
    const backButton = document.querySelector('.back-button');
    if (backButton) {
        backButton.addEventListener('click', function (e) {
            e.preventDefault();
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = 'index.html'; // Fallback
            }
        });
    }

    const backButton2 = document.querySelector('.back-button2');
    if (backButton2) {
        backButton2.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = 'index.html';
        });
    }
    

    // --- YOUR EXISTING REGISTRATION FORM LOGIC (REFACTORED) ---
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", async function (e) {
            e.preventDefault(); // Prevent default submission immediately

            const form = e.target; // Same as 'this' in this context
            const patterns = {
                fullname: /^[A-Za-z\s]{2,50}$/,
                username: /^[a-zA-Z0-9._-]{4,20}$/,
                idnumber: /^\d{6,10}$/,
                email: /^[a-zA-Z0-9._%+-]+@wvsu\.edu\.ph$/i,
                dob: /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/(19\d\d|20\d\d)$/,
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

            // 1. Empty field checks
            for (const key in values) {
                if (values.hasOwnProperty(key)) {
                    if (!values[key]) { // Check if the value is empty
                        let fieldName = key.replace(/([A-Z])/g, " $1"); // Simple way to add space before capitals
                        fieldName = fieldName.charAt(0).toUpperCase() + fieldName.slice(1);
                        alert(`${fieldName} is required.`);
                        return; 
                    }
                }
            }

            // 2. Pattern validation
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
                    alert(errorMessage);
                    return; 
                }
            }

            // 3. Date of Birth - check actual date validity
            const dobParts = values.dob.split('/');
            const month = parseInt(dobParts[0], 10);
            const day = parseInt(dobParts[1], 10);
            const year = parseInt(dobParts[2], 10);
            const dateObj = new Date(year, month - 1, day); 

            if (!(dateObj.getFullYear() === year && dateObj.getMonth() === (month - 1) && dateObj.getDate() === day)) {
                alert('Invalid Date of Birth. Please ensure it is a valid calendar date (e.g., not 02/30/2000).');
                return; 
            }

            // 4. Password length
            if (values.password.length < 8) {
                alert('Password must be at least 8 characters long.');
                return; 
            }

            // If all client-side validations pass, proceed with form submission
            console.log("Client-side validation passed. Submitting form...");
            try {
                const formData = new FormData(form);
                const response = await fetch("register.php", {
                    method: "POST",
                    body: formData
                });
                // Check if response is ok and content type is JSON before parsing
                if (!response.ok) {
                    // Handle HTTP errors (e.g., 404, 500)
                    const errorText = await response.text(); // Get error text from server if available
                    throw new Error(`Server responded with ${response.status}: ${errorText || response.statusText}`);
                }
                
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    const result = await response.json();
                    if (result.success) {
                        if (result.redirect) {
                            window.location.href = result.redirect;
                        } else {
                            alert(result.message || "Registration successful!"); 
                        }
                    } else {
                        alert(result.message || "Registration failed. Please try again."); 
                    }
                } else {
                    // Handle non-JSON responses if PHP script might return HTML/text on error
                    const textResult = await response.text();
                    console.error("Non-JSON response from server:", textResult);
                    alert("Received an unexpected response from the server. Please check console for details.");
                }
            } catch (error) {
                console.error("Error during form submission:", error);
                alert(`An error occurred while trying to register: ${error.message}. Please check your connection and try again.`);
            }
        });
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
    
// WVSU Overlay logic
const wvsuLink = document.getElementById('navWvsu');
const wvsuOverlay = document.getElementById('wvsuOverlay');
const closeWvsu = document.getElementById('closeWvsu');

if (wvsuLink && wvsuOverlay && closeWvsu) {
    wvsuLink.addEventListener('click', (e) => {
        e.preventDefault();
        wvsuOverlay.style.display = 'flex';
    });

    closeWvsu.addEventListener('click', () => {
        wvsuOverlay.style.display = 'none';
    });
}

// CON Overlay logic
const conLink = document.getElementById('navCon');
const conOverlay = document.getElementById('ConOverlay');
const closeCon = document.getElementById('closeCon');

if (conLink && conOverlay && closeCon) {
    conLink.addEventListener('click', (e) => {
        e.preventDefault();
        conOverlay.style.display = 'flex';
    });

    closeCon.addEventListener('click', () => {
        conOverlay.style.display = 'none';
    });

}

const accessibilityTrigger = document.getElementById("accessibilityTrigger");
const accessibilityOverlay = document.getElementById("accessibilityOverlay");
const accessibilityContent = document.getElementById("accessibilityContent"); // Still useful for reference if needed
const closeAccessibilityContent = document.getElementById("closeAccessibilityContent");

if (accessibilityTrigger && accessibilityOverlay && closeAccessibilityContent) {

    accessibilityTrigger.addEventListener("click", (event) => {
        event.preventDefault(); // Important if your trigger is an <a> tag
        if (accessibilityOverlay.style.display === 'none' || accessibilityOverlay.style.display === '') {
            accessibilityOverlay.style.display = 'flex'; // Show the overlay
        } else {
            accessibilityOverlay.style.display = 'none'; // Hide the overlay
        }
    });

    // Close when ✖ button is clicked
    closeAccessibilityContent.addEventListener("click", () => {
        accessibilityOverlay.classList.remove("active");
        accessibilityOverlay.style.display = 'none'; // Hide the overlay
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('navSearch');
    const suggestionsContainer = document.getElementById('suggestionsContainer');

    // STEP 1: MANUALLY DEFINE YOUR SEARCHABLE ITEMS AND THEIR LINKS HERE
    // This is your "cheat sheet" that you configure directly.
    const searchableItems = [
        { text: "MOTHER'S HEALING", url: "mothers_healing.html" },
        { text: "INFANT CARE", url: "infant_care.html" },
        { text: "FEEDING SUPPORT", url: "feeding_support.html" },
        { text: "WARNING SIGNS", url: "warning_signs.html" },
        { text: "NUTRITION AND LIFESTYLE", url: "nutrition_lifestyle.html" }, 
        { text: "FAMILY AND SUPPORT SYSTEM", url: "family_support.html" },
        { text: "EMERGENCY HOTLINE", url : "emergency-hotlines.html"},
        { text: "FREQUENTLY ASK QUESTION", url : "faq.html"},
        { text: "PREMIUM MEMBERS", url : "premium-members.html"},
        { text: "TERMS AND CONDITIONS", url : "Terms_and_Conditions.html"},
        { text: "PLANNER", url : "planner.html"},
        { text: "LOGIN", url : "login_student.html"},
        { text: "REGISTER", url : "register.html"},
    ];

    // (The part that looped through 'moduleButtons' to build 'searchableItems' is now removed)

    function positionSuggestions() {
        if (!searchInput || !suggestionsContainer) return;
        const inputRect = searchInput.getBoundingClientRect();
        const bodyRect = document.body.getBoundingClientRect();
        suggestionsContainer.style.right = '25%' ;
        suggestionsContainer.style.top = (inputRect.bottom - bodyRect.top) + 'px';
        suggestionsContainer.style.width = searchInput.offsetWidth + 'px';
    }

    if (searchInput && suggestionsContainer) {
        positionSuggestions();
        window.addEventListener('resize', positionSuggestions);

        searchInput.addEventListener('input', function() {
            // Clean up the typed search term (remove leading/trailing quotes)
            let typedValue = this.value.trim().toLowerCase();
            if (typedValue.startsWith("'") || typedValue.startsWith('"')) {
                typedValue = typedValue.substring(1);
            }
            if (typedValue.endsWith("'") || typedValue.endsWith('"')) {
                typedValue = typedValue.substring(0, typedValue.length - 1);
            }
            const currentSearchTerm = typedValue;

            suggestionsContainer.innerHTML = ''; // Clear previous suggestions

            if (currentSearchTerm.length === 0) {
                suggestionsContainer.style.display = 'none';
                return;
            }

            const filteredItems = searchableItems.filter(item =>
                item.text.toLowerCase().includes(currentSearchTerm) // Search based on the 'text' in your array
            );

            if (filteredItems.length > 0) {
                filteredItems.forEach(item => {
                    const suggestionElement = document.createElement('a');
                    suggestionElement.classList.add('suggestion-item');
                    suggestionElement.href = item.url; // Link uses the 'url' from your array

                    // Display the 'text', highlighting the search term
                    const regex = new RegExp(`(${currentSearchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    suggestionElement.innerHTML = item.text.replace(regex, '<strong>$1</strong>');

                    suggestionsContainer.appendChild(suggestionElement);
                });
                suggestionsContainer.style.display = 'block';
                positionSuggestions();
            } else {
                suggestionsContainer.style.display = 'none';
            }
        });

        document.addEventListener('click', function(event) {
            if (suggestionsContainer && searchInput && !searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });

        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (suggestionsContainer && !suggestionsContainer.contains(document.activeElement)) {
                    suggestionsContainer.style.display = 'none';
                }
            }, 150);
        });

        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (suggestionsContainer) {
                    suggestionsContainer.style.display = 'none';
                }
                searchInput.blur();
            }
        });
    } else {
        if (!searchInput) console.error("Search input 'navSearch' not found.");
        if (!suggestionsContainer) console.error("Suggestions container 'suggestionsContainer' not found.");
    }
});
function positionSuggestions() {
    if (!searchInput || !suggestionsContainer) return;
    suggestionsContainer.style.top = searchInput.offsetHeight + 'px'; // Or rely on CSS top: 100%
    suggestionsContainer.style.left = '0'; // Should be 0 relative to wrapper
    suggestionsContainer.style.width = searchInput.offsetWidth + 'px';
}