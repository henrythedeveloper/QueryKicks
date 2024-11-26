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

        <div class="contact-form">
            <h2>Send us a Message</h2>
            <form id="contact-form">
                <div class="form-group">
                    <label for="contact-name">Name</label>
                    <input type="text" id="contact-name" name="name" value="<?= htmlspecialchars($_SESSION['name']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <select id="subject" name="subject" required>
                        <option value="">Select a subject</option>
                        <option value="general">General Question</option>
                        <option value="technical">Technical Support</option>
                        <option value="feedback">Feedback</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>

                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>

        <div class="support-hours">
            <h2>Support Hours</h2>
            <div class="hours-list">
                <div class="hours-item">
                    <span>Monday - Friday:</span>
                    <span>9am - 8pm EST</span>
                </div>
                <div class="hours-item">
                    <span>Saturday:</span>
                    <span>10am - 6pm EST</span>
                </div>
                <div class="hours-item">
                    <span>Sunday:</span>
                    <span>12pm - 5pm EST</span>
                </div>
            </div>
            <p class="note">* Our AI Clerk is available 24/7 to assist you!</p>
        </div>
    </div>
</div>