function getNavBar() {
    // Define the fetch logic as a function
    const fetchData = () => {
        // Define the data to send
        let params = {node: 'navbar'};
  
        // Fetch the JSON data from the server
        fetch('node.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(params),
        })
        .then(response => {
            // Parse the response body as text instead of JSON
            return response.text();
        })
        .then(text => {
            // Try to parse the text as JSON
            try {
                const navData = JSON.parse(text);
  
                // Initialize the NavigationBar class with the fetched JSON data
                const navBar = new NavigationBar(navData);
  
                // Render the navigation bar
                navBar.render();
            } catch (error) {
                // If the text could not be parsed as JSON, log it to the console
                console.log('Response text: ', text);
            }
        })
        .catch(error => {
            // Log any errors to the console
            console.log('Fetchs error: ', error);
        });
    }
  
    // Check if the DOM is already loaded
    if (document.readyState === "loading") {
        // If the DOM is still loading, add the listener
        document.addEventListener('DOMContentLoaded', fetchData);
    } else {
        // If the DOM has already loaded, just call the function
        fetchData();
    }
  }
  
  getNavBar();
  