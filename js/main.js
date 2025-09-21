window.addEventListener('load', function() {
  Fancybox.bind("[data-fancybox]", {
    // Your custom options
  });

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