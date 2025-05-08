document.addEventListener('DOMContentLoaded', function () {
    // Main back button - navigates back in history
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
   
    // Get all topic buttons
    const topicButtons = document.querySelectorAll('.topic-button');
   
    // Add click event listeners to each button
    topicButtons.forEach(button => {
      button.addEventListener('click', () => {
        const topic = button.getAttribute('data-topic');
        openModal(topic);
      });
    });
  });
  
  // Function to open the modal for a specific topic
  function openModal(topic) {
    // Get the appropriate modal
    let modal;
   
    if (topic === 'physiological') {
      modal = document.getElementById('physiological-modal');
    } else if (topic === 'perineal') {
      modal = document.getElementById('perineal-modal');
    } else if (topic === 'depression') {
      modal = document.getElementById('depression-modal');
    } else if (topic === 'medication') {
      modal = document.getElementById('medication-modal');
    } else if (topic === 'anxiety') {
      modal = document.getElementById('anxiety-modal');
    } else if (topic === 'sexual') {
      modal = document.getElementById('sexual-modal');
    } else if (topic === 'coping') {
      modal = document.getElementById('coping-modal');
    } else if (topic === 'hydration') {
      modal = document.getElementById('hydration-modal');
    } else if (topic === 'exercise') {
      modal = document.getElementById('exercise-modal');
    } else {
      // For any other topics, use the template modal
      modal = document.getElementById('template-modal');
      
      // Update the template modal subtitle based on the clicked topic
      const topicButton = document.querySelector(`[data-topic="${topic}"]`);
      if (topicButton) {
        const topicTitle = topicButton.querySelector('h3')?.textContent;
        const modalSubtitle = modal.querySelector('.modal-subtitle');
        if (topicTitle && modalSubtitle) {
          modalSubtitle.textContent = topicTitle;
        }
      }
    }
   
    // Show the modal if it exists
    if (modal) {
      modal.style.display = 'block';

      // Modal back button - closes the modal
      const backButton2 = modal.querySelector('.back-button2');
      if (backButton2) {
        // Remove existing event listeners to prevent duplicates
        const newBackButton = backButton2.cloneNode(true);
        backButton2.parentNode.replaceChild(newBackButton, backButton2);
        
        newBackButton.addEventListener('click', () => {
          modal.style.display = 'none';
        });
      }
    } else {
      console.error(`Modal for topic "${topic}" not found`);
    }
  }