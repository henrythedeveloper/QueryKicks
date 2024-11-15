<?php include './views/layouts/header.php'; ?>

<div class="container login-background">
    <header class="header-section">
        <img src="./assets/store-clerk/store-clerk-3-mosaiced.webp" alt="Store Clerk" class="clerk">
        <div class="header-item">
            <p>Clerk is talking</p>
        </div>
    </header>
    
    <main>
        <div class="main-content">
            <div class="navbar">
                <button class="tab-button" onclick="openTab(event, 'shoes')">Shoes</button>
                <button class="tab-button" onclick="openTab(event, 'cart')">Cart</button>
                <button class="tab-button" onclick="openTab(event, 'checkout')">Checkout</button>
                <button class="tab-button" onclick="openTab(event, 'contact')">Contact</button>
                <button class="tab-button" onclick="openTab(event, 'about')">About</button>
                <button class="tab-button" onclick="openTab(event, 'faq')">FAQ</button>
                <button class="logout-button" onclick="logout(event, 'logout')">Leave</button>
            </div>

            <!-- The Modal -->
            <div id="myModal" class="modal">

            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <p>Some text in the Modal..</p>
            </div>
            </div>
            
            <div id="shoes" class="tab-content">
                <?php include 'views/store/products.php'; ?>
            </div>
            <div id="cart" class="tab-content">
                <?php include 'views/store/cart.php'; ?>
            </div>
            <div id="checkout" class="tab-content">
                <?php include 'views/store/checkout.php'; ?>
            </div>
            <div id="contact" class="tab-content">
                <?php include 'views/store/contact.php'; ?>
            </div>
            <div id="about" class="tab-content">
                <?php include 'views/store/about.php'; ?>
            </div>
            <div id="faq" class="tab-content">
                <?php include 'views/store/faq.php'; ?>
            </div> 
        </div>
    </main>
</div>

<?php include './views/layouts/footer.php'; ?>
