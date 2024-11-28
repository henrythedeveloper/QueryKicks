<?php
/**
 * contact.php: This file serves as the contact view for the Query Kicks application. 
 * It provides users with information on how to reach out to the support team and includes 
 * details about support hours and response times.
 *
 * Features:
 *  - **Contact Information**: Displays an email address for support and the expected response time.
 *  - **Support Hours**: Lists the operating hours for live support and highlights the availability of an AI clerk.
 *  - **Encourages User Interaction**: Invites users to share their questions and ideas about virtual sneaker collections.
 *
 * Static Content:
 *  - Email: `support@querykicks.com` (not real just an example)
 *
 * Linked Assets:
 *  - Icons (e.g., `email-icon`, `time-icon`): Expected to use CSS or an external library for styling.
 *  - Additional styling for `.contact-container` and its child elements should be present in the associated stylesheet.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */
?>

<div class="contact-container">
    <h1>Contact QueryKicks</h1>
    <div class="contact-content">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <p>Have questions about your virtual kicks? Want to share your collection ideas? We'd love to hear from you!</p>
            
            <div class="contact-details">
                <div class="contact-item">
                    <i class="email-icon"></i>
                    <p>support@querykicks.com</p>
                </div>
                <div class="contact-item">
                    <i class="time-icon"></i>
                    <p>Response Time: Within 24 hours</p>
                </div>
            </div>
        </div>

        <div class="support-hours">
            <h2>Support Hours</h2>
            <div class="hours-list">
                <div class="hours-item">
                    <span>Monday - Friday:</span>
                    <span>9am - 8pm MST</span>
                </div>
                <div class="hours-item">
                    <span>Saturday:</span>
                    <span>10am - 6pm MST</span>
                </div>
                <div class="hours-item">
                    <span>Sunday:</span>
                    <span>12pm - 5pm MST</span>
                </div>
            </div>
            <p class="note">* Our AI Clerk is available 24/7 to assist you!</p>
        </div>
    </div>
</div>