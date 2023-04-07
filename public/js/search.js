window.addEventListener('load', () => { // on page load
    const searchText = document.querySelector("#searchText");
    const searchMethod = document.querySelector("#searchBy");

    searchMethod.addEventListener('change', () => {
        switch (searchMethod.value) {
            case "name":
                searchText.type = "text";
                break;

            case "date":
                searchText.type = "date";
                break;
        }
    });


});