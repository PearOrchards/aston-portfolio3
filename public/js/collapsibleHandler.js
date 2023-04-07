window.addEventListener('load', () => { // on page load
    // For each .collapseHeader (the header of a collapsible element), add an event listener to toggle the open class.
    document.querySelectorAll('.collapseHeader').forEach((collapsible) => {
        collapsible.addEventListener('click', () => {
            collapsible.parentElement.classList.toggle("open");
        });
    });
});