import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Register() {
    useEffect(() => {
        // Init scripts
    }, []);

    return (
        


<>
    <div className="auth-container">
        <div className="auth-left">
            <div className="auth-left-content">
                <h1>Nova<span>Mart</span></h1>
                <p>Bangladesh's leading multi-vendor NovaMart platform. Shop from thousands of verified sellers.</p>
                <div className="features">
                    <div className="feature">
                        <i className="fas fa-shield-alt"></i>
                        <div><h4>Secure Shopping</h4><p>100% secure payment processing</p></div>
                    </div>
                    <div className="feature">
                        <i className="fas fa-truck"></i>
                        <div><h4>Fast Delivery</h4><p>Free shipping on orders over ৳2000</p></div>
                    </div>
                    <div className="feature">
                        <i className="fas fa-undo"></i>
                        <div><h4>Easy Returns</h4><p>7 days hassle-free return policy</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div className="auth-right">
            <div className="auth-form">
                <h2>Create Account</h2>
                <p className="subtitle">Join NovaMart and start shopping</p>
                <form>
                    <div className="form-group">
                        <label htmlFor="name">Full Name</label>
                        <input type="text" id="name" name="name" className="form-control" placeholder="Enter your full name" required autofocus />
                    </div>
                    <div className="form-group">
                        <label htmlFor="email">Email Address</label>
                        <input type="email" id="email" name="email" className="form-control" placeholder="Enter your email" required />
                    </div>
                    <div className="form-group">
                        <label htmlFor="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" className="form-control" placeholder="Enter your phone number" />
                    </div>
                    <div className="form-group">
                        <label htmlFor="password">Password</label>
                        <input type="password" id="password" name="password" className="form-control" placeholder="Create a password" required />
                    </div>
                    <div className="form-group">
                        <label htmlFor="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" className="form-control" placeholder="Confirm your password" required />
                    </div>
                    <div className="checkbox-group ex-style-184">
                        <label><input type="checkbox" name="terms" required /> I agree to the <a href="#" className="ex-style-117">Terms & Conditions</a></label>
                    </div>
                    <button type="submit" className="btn btn-primary">
                        Create Account <i className="fas fa-arrow-right ex-style-70"></i>
                    </button>
                </form>
                <div className="divider"><span>or continue with</span></div>
                <div className="social-login">
                    <a href="#" className="social-btn google"><i className="fab fa-google"></i></a>
                    <a href="#" className="social-btn facebook"><i className="fab fa-facebook-f"></i></a>
                </div>
                <p className="auth-footer">Already have an account? <a href="login.html">Sign In</a></p>
            </div>
        </div>
    </div>
</>


    );
}
