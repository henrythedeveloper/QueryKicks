<?php
/**
 * about.php: This file serves as the "About" view for the Query Kicks application. 
 * It provides information about the platform's mission, features, and future plans.
 *
 * Features:
 *  - **Our Story**: Describes the origins and mission of Query Kicks.
 *  - **Key Features**: Highlights the platform's unique selling points, such as virtual currency, an AI store clerk, and digital collections.
 *  - **AI Clerk Introduction**: Introduces the AI store clerk and outlines its capabilities, such as 24/7 availability and personalized recommendations.
 *  - **Looking Ahead**: Shares future plans for the platform, including new features, limited releases, and community events.
 *
 * Static Content:
 *  - Textual descriptions of the platform's mission, features, and vision.
 *  - List of upcoming features and enhancements.
 *  - Visual elements for features and clerk descriptions (e.g., icons for virtual currency, AI store clerk, and collections).
 *
 * Linked Assets:
 *  - CSS for styling elements like `about-container`, `features-grid`, and `clerk-info`.
 *  - Icons referenced via classes like `currency-icon`, `ai-icon`, and `collection-icon` should be included in the project assets.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */
?>
<div class="about-container">
    <h1>About QueryKicks</h1>
    <div class="about-content">
        <div class="about-section">
            <h2>Our Story</h2>
            <p>Welcome to QueryKicks, where the world of sneaker collecting meets virtual reality! Founded in 2024, we've created a unique platform that lets sneaker enthusiasts explore and collect their dream shoes in a virtual space.</p>
            
            <p>Our mission is to make the thrill of sneaker collecting accessible to everyone, regardless of budget or location. Through our innovative virtual platform, we've created a space where passion for sneakers can flourish without limits.</p>
        </div>

        <div class="about-features">
            <h2>What Makes Us Different</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"></div>
                    <i class="currency-icon"></i>
                    <h3>Virtual Currency</h3>
                    <p>Build your collection using our virtual currency system</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon"></div>
                    <i class="ai-icon"></i>
                    <h3>AI Store Clerk</h3>
                    <p>Get personalized assistance from our AI clerk</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon"></div>
                    <i class="collection-icon"></i>
                    <h3>Digital Collection</h3>
                    <p>Manage and showcase your virtual sneaker collection</p>
                </div>
            </div>
        </div>

        <div class="about-clerk">
            <h2>Meet Our AI Clerk</h2>
            <div class="clerk-info">
                <div class="clerk-description">
                    <p>Our AI clerk is more than just a virtual assistant - they're your personal guide to the world of QueryKicks. With extensive knowledge of our collection and a friendly personality, they're here to help you find the perfect additions to your virtual collection.</p>
                    
                    <ul class="clerk-features">
                        <li>Available 24/7</li>
                        <li>Personalized recommendations</li>
                        <li>Real-time assistance</li>
                        <li>Collection insights</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="about-future">
            <h2>Looking Ahead</h2>
            <p>We're constantly working to enhance your virtual collecting experience. Stay tuned for exciting new features, exclusive releases, and community events!</p>
            
            <div class="coming-soon">
                <h3>Coming Soon</h3>
                <ul>
                    <li>Collection Showcases</li>
                    <li>Trading System</li>
                    <li>Limited Edition Releases</li>
                    <li>Community Events</li>
                </ul>
            </div>
        </div>
    </div>
</div>