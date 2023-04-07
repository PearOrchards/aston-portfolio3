<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once __DIR__ . '/components/head.php'; ?>
    <title>AProject Home</title>
    <link rel="stylesheet" href="./public/styles/home.css">
</head>
<body>
<?php require_once __DIR__ . '/components/navbar.php'; ?>
<header id="main-header">
    <div class="col">
        <h1>AProject</h1>
        <h2>See all the projects that we're working on!</h2>
    </div>
</header>
<main>
    <section class="goProjects">
        <div class="row">
            <h2>See our Projects</h2>
            <a href="projects.php">
                <button>Projects <i class="fa-solid fa-arrow-right"></i></button>
            </a>
        </div>
    </section>
    <section class="skills">
        <!-- Each article contains an icon of the language, a brief review of the language, and a star rating -->
        <h2>Our Skills</h2>
        <div class="row">
            <article class="skill">
                <i class="fa-brands fa-square-js big-icon"></i>
                <h3>JavaScript</h3>
                <p>The first language we go to when we make new projects!</p>
                <div class="stars">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star-half-stroke"></i>
                </div>
            </article>
            <article class="skill">
                <i class="fa-brands fa-java big-icon"></i>
                <h3>Java</h3>
                <p>Our go-to language for desktop applications.</p>
                <div class="stars">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star-half-stroke"></i>
                    <i class="fa-regular fa-star"></i>
                </div>
            </article>
            <article class="skill">
                <div class="row forceRow">
                    <i class="fa-brands fa-html5 big-icon"></i>
                    <i class="fa-solid fa-plus big-icon"></i>
                    <i class="fa-brands fa-css3-alt big-icon"></i>
                </div>
                <h3>HTML5 + CSS3</h3>
                <p>Basically the only option for front-end.</p>
                <div class="stars">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                </div>
            </article>
            <article class="skill">
                <i class="fa-brands fa-php big-icon"></i>
                <h3>PHP</h3>
                <p>I mean it's alright.</p>
                <div class="stars">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star-half-stroke"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                </div>
            </article>
        </div>
    </section>
    <section class="projects">
        <!-- Each article contains the company name, a testimonial as a blockquote, and a dropper to further stylise the blockquote -->
        <h2>Previous Clients</h2>
        <div class="row">
            <article class="project">
                <blockquote>We reached out to Aston to build a new website for an upcoming game, and not only were they <em>easy to talk to</em>, they had <em>super quick turnarounds!</em></blockquote>
                <div class="dropper"></div>
                <h3>Team Cherry</h3>
            </article>
            <article class="project">
                <blockquote>Aston helped us redo our front-end with Next.JS, and our website preforms <em>twice as fast</em> because of it. We <em>cannot recommend them enough</em>.</blockquote>
                <div class="dropper"></div>
                <h3>ODEON</h3>
            </article>
            <article class="project">
                <blockquote>After countless complaints about our old system, we asked Aston for help. And they were <em>a joy to talk to</em>, and returned to us a <em>highly optimised</em> system.</blockquote>
                <div class="dropper"></div>
                <h3>Unite Students</h3>
            </article>
        </div>
    </section>
    <section class="about-us">
        <!-- Each article contains an image, the name, and their education -->
        <h2>Meet the Team</h2>
        <div class="row">
            <article class="profile">
                <img class="founder-image" src="public/images/me.webp" alt="An image of Pawel">
                <h3>Pawel, Front-end</h3>
                <p class="education">Aston University</p>
            </article>
            <article class="profile">
                <img class="founder-image" src="public/images/placeholder.webp" alt="An placeholder for the image of Alex">
                <h3>Alex, Back-end</h3>
                <p class="education">Aston University</p>
            </article>
        </div>
    </section>
    <section class="why-delay">
        <h2>So why delay?</h2>
        <h3>Get in touch today!</h3>
    </section>
</main>
<?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>