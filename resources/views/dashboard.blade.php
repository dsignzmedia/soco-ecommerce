<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phase 1 – Portal Flow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <div class="logo">SOCO Uniform Portal</div>
        <nav>
            <a href="#">Home</a>
            <a href="#">Our Services</a>
            <a href="#">Customization</a>
            <a href="#">Customer Care</a>
            <a href="#">FAQs</a>
            <a href="#">About Us</a>
            <a href="#">Login / Student Login</a>
            <a href="#">Register</a>
        </nav>
    </header>

    <section class="hero">
        <h1>Home → Login → Purchase → Checkout → Exchange / Refund</h1>
        <p>Store hours: Mon–Fri 9 AM–6 PM • Orders outside hours queue for next business day • Stock depends on school allocation.</p>
        <p>Customer support dedicated to parents and students.</p>
    </section>

    <section class="section">
        <h2>Homepage Sections</h2>
        <div class="grid-4">
            <div class="card">Banner / Introduction</div>
            <div class="card">Our Services</div>
            <div class="card">Featured Products</div>
            <div class="card">School List</div>
            <div class="card">About Section</div>
            <div class="card">Customer Support</div>
            <div class="card">Footer: Return / Exchange Policy, Privacy Policy, Terms &amp; Conditions</div>
        </div>
    </section>

    <section class="section">
        <h2>Parent Login / Student Registration</h2>
        <div class="grid-4">
            <div class="card">
                <strong>Login / Registration</strong>
                <ul>
                    <li>Parent Login</li>
                    <li>New User Registration</li>
                    <li>Student Profile Creation</li>
                    <li>Add Multiple Students</li>
                </ul>
            </div>
            <div class="card">
                <strong>After Login</strong>
                <ul>
                    <li>School List</li>
                    <li>Uniform Products</li>
                    <li>Customization</li>
                    <li>Size Guide</li>
                    <li>Add to Cart</li>
                </ul>
            </div>
            <div class="card">
                <strong>Cart &amp; Checkout</strong>
                <ol>
                    <li>View / Edit Cart</li>
                    <li>Proceed to Checkout</li>
                    <li>Enter Address &amp; Contact</li>
                    <li>Select Payment Method</li>
                    <li>Order Confirmation</li>
                </ol>
            </div>
            <div class="card">
                <strong>Important Notes</strong>
                <ul>
                    <li>Complete student profile (School, Grade, Section, Gender) before checkout</li>
                    <li>Parent can register multiple students</li>
                    <li>“Proceed to Checkout” appears only after details are complete</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section">
        <h2>Student Registered Dashboard</h2>
        <div class="profile-header">
            <img class="profile-img" src="https://via.placeholder.com/100" alt="Student">
            <div>
                <p><strong>Student Name:</strong> Ashley Rivera</p>
                <p><strong>School:</strong> Greenfield Academy</p>
                <p>Grade 6 • Section A • Female</p>
            </div>
            <div style="margin-left:auto;">
                <strong>Navigation</strong>
                <p>Home · Our Services · Customization · FAQs · Customer Care · About Us · My Orders · Logout</p>
            </div>
        </div>

        <div class="grid-4" style="margin-top:1.5rem;">
            @foreach (range(1,4) as $product)
                <div class="card">
                    <img src="https://via.placeholder.com/300x200" alt="Product" style="width:100%;border-radius:8px;margin-bottom:0.5rem;">
                    <h3>Product Name</h3>
                    <select style="width:100%;padding:0.4rem;border-radius:8px;border:1px solid #cfd6e6;margin:0.5rem 0;">
                        <option>Select Size</option>
                        <option>XS</option>
                        <option>S</option>
                        <option>M</option>
                        <option>L</option>
                        <option>XL</option>
                    </select>
                    <button>Add to Cart</button>
                </div>
            @endforeach
        </div>
        <p style="margin-top:1rem;">Catalog filtered by School · Grade · Gender.</p>
    </section>

    <section class="section">
        <h2>My Orders – Process Flow</h2>
        <div class="status-timeline">
            <span class="active">Order Placed</span>
            <span class="active">Order Accepted</span>
            <span>Order Packed</span>
            <span>Out for Delivery</span>
            <span>Delivered</span>
            <span>Completed</span>
        </div>

        <div style="margin-top:1.5rem;">
            <p><strong>Order #SOCO-2025-0001</strong> · Placed Nov 19, 2025</p>
            <p>Price Summary · Delivery Address · Payment Method</p>
            <div style="margin-top:1rem;">
                <button>Request Exchange / Refund</button>
                <button class="secondary" style="margin-left:0.5rem;">View Details</button>
            </div>
        </div>

        <div style="margin-top:1.5rem;">
            <strong>Exchange / Refund Steps</strong>
            <ol>
                <li>User clicks Request Exchange / Refund</li>
                <li>Select Reason from dropdown</li>
                <li>Upload Photograph (optional)</li>
                <li>Submit &amp; receive confirmation</li>
                <li>Status updates to Exchange Requested / Refund Requested</li>
                <li>Company policy determines approval; notifications via portal/email</li>
            </ol>
        </div>
    </section>

    <footer class="section">
        <h2>Customer Support &amp; Policies</h2>
        <p>Return / Exchange Policy · Privacy Policy · Terms &amp; Conditions</p>
        <p>Support available for parents and students during business hours.</p>
    </footer>
</body>
</html>

