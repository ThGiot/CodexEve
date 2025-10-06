function getNavItem() {
    // Define the fetch logic as a function
    const fetchData = () => {
        // Define the data to send
        let params = {node: 'navitem'};
  
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
                let navData = JSON.parse(text);
                // If navData is not an array, put it into one
                if (!Array.isArray(navData)) {
                    navData = [navData];
                }
                // Création d'une nouvelle instance de NavItemList
                let navItemList = new NavItemList();
                // Boucle à travers les données JSON pour ajouter les éléments à la liste
                navData.forEach(item => {
                    navItemList.addItem(item.icon, item.title, item.link);
                });
        
                // Affichage du HTML généré
                document.getElementById("navitem").innerHTML = navItemList.render();
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
  
  getNavItem();
  




