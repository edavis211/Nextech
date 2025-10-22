window.addEventListener('load', function() {
  Fancybox.bind("[data-fancybox]", {
    // Your custom options
  });

  // Handle search form submission
  const searchForm = document.getElementById('keyword-search');
  if (searchForm) {
    searchForm.addEventListener('submit', function(event) {
      event.preventDefault();
      const searchInput = this.querySelector('input[name="search"]');
      const searchValue = searchInput.value.trim();
      
      if (searchValue) {
        const baseUrl = this.action;
        const searchUrl = `${baseUrl}?search=${encodeURIComponent(searchValue)}`;
        window.location.href = searchUrl;
      } else {
        // If no search value, just go to the resource library page
        window.location.href = this.action;
      }
    });
  }

  // selcet all links with data-copy-url attribute
  const copyUrlLinks = document.querySelectorAll('a[data-copy-url]');

  copyUrlLinks.forEach(link => {
    link.addEventListener('click', function(event) {
      event.preventDefault();
      const urlToCopy = this.href;

      navigator.clipboard.writeText(urlToCopy).then(function() {
        // Success feedback (optional)
        alert('URL copied to clipboard!');
      }, function(err) {
        // Error feedback (optional)
        console.error('Could not copy text: ', err);
      });
    });
  });
});