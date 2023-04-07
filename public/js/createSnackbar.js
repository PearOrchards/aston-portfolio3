/**
 * Creates a snackbar element.
 * @param severity {0 | 1 | 2} The severity of the message. 0 = info, 1 = warning, 2 = error
 * @param message {String} The message to display to the user
 */
function createSnackbar(severity, message) {
    // Going to be totally honest, I used this like 5 projects ago, and I'm really surprised it still works.
    const snackbar = document.createElement("div");
    snackbar.classList.add("snackbar", "hidden");

    const row = document.createElement("div");
    row.classList.add("row");

    const ico = document.createElement("i");
    ico.classList.add("fas");
    const subtext = document.createElement("p");
    subtext.classList.add("subtext");
    subtext.innerText = message;
    const close = document.createElement("i");
    close.classList.add("fas", "fa-times");
    close.setAttribute("onClick", `javascript: closeBar(this.offsetParent)`);

    const progress = document.createElement("div");
    progress.classList.add("progress");

    switch (severity) {
        case 0:
            ico.classList.add("fa-circle-info");
            break;

        case 1:
            ico.classList.add("fa-exclamation-triangle");
            snackbar.classList.add("flashYellow");
            break;

        case 2:
            ico.classList.add("fa-exclamation-circle");
            snackbar.classList.add("flashRed");
            break;
    }

    row.append(ico, subtext, close);
    snackbar.append(row, progress);
    document.body.append(snackbar);

    setTimeout(() => {
        snackbar.classList.remove("hidden");
        goBar(snackbar);
    })
}

/**
 * Starts the animation for the progress bar.
 * @param snackbar {HTMLDivElement} The snackbar element.
 */
function goBar(snackbar) {
    const bar = snackbar.children[1];
    bar.classList.add("go");

    setTimeout(() => {
        closeBar(snackbar);
    }, 5000);
}

/**
 * Closes the snackbar.
 * @param snackbar {HTMLDivElement} The snackbar element.
 */
function closeBar(snackbar) {
    const bar = snackbar.children[1];
    bar.classList.remove("go");
    snackbar.classList.add("hidden");

    setTimeout(() => {
        snackbar.remove();
    }, 1000);
}

