window.addEventListener('load', () => { // on page load
    const searchText = document.querySelector("#searchText");
    const searchMethod = document.querySelector("#searchBy");

    searchMethod.addEventListener('change', () => {
        switch (searchMethod.value) {
            case "name":
                searchText.type = "text";
                break;

            case "startDate":
            case "endDate":
                searchText.type = "date";
                break;
        }
    });


});