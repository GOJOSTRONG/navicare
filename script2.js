   // Function to show content template
    function showContent(contentId) {
        // Hide main options
        document.getElementById('main-options').style.display = 'none';
        
        // Show selected content
        document.getElementById(contentId).style.display = 'block';
      }
      
      // Function to return to main options
      function showMainOptions() {
        // Hide all content templates
        const contentTemplates = document.querySelectorAll('.content-template');
        contentTemplates.forEach(template => {
          template.style.display = 'none';
        });
        
        // Show main options
        document.getElementById('main-options').style.display = 'grid';
      }
