function moduleChange(module_id){
    let params = {module_id : module_id};

    // Fetch the HTML content from the server
    fetch('module_change.php', {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        },
        body: JSON.stringify(params),
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.result === 'true') {
                getContent();
                getNavBar();
                getNavItem();
            } else {
                // do nothing
            }
        })
        .catch(error => {
            console.log('Fetch error: ', error);
        });
}