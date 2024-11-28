<?php
/**
 * faq.php: This file serves as the FAQ (Frequently Asked Questions) view for the Query Kicks application. 
 * It provides answers to common user questions about the platform and its features.
 *
 * Features:
 *  - **Expandable FAQ Items**: Each question can be expanded or collapsed to show or hide the corresponding answer.
 *  - **General Questions Section**: Includes questions and answers about Query Kicks and its virtual currency system.
 *
 * Data Dependencies:
 *  - No dynamic data dependencies; all content is static and hardcoded into the view.
 *
 * Linked Assets:
 *  - Expected to use CSS for styling expandable sections.
 *  - JavaScript may be required to toggle the visibility of answers via the `faq-toggle` class.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */
?>

<div class="faq-container">
    <h1>Frequently Asked Questions</h1>
    <div class="faq-content">
        <div class="faq-section">
            <h2>General Questions</h2>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What is QueryKicks?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>QueryKicks is a virtual sneaker collecting platform where you can build your dream sneaker collection using virtual currency.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>How does the virtual currency work?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>You can add virtual currency to your account using the "Add Money" button. This currency can be used to purchase virtual shoes for your collection.</p>
                </div>
            </div>
        </div>
    </div>
</div>