import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Login() {
    useEffect(() => {
        // Init scripts
    }, []);

    return (
        


<>
    <div className="auth-container">
        <div className="auth-left">
            <div className="auth-left-content">
                <h1>Nova<span>Mart</span></h1>
                <p>Bangladesh's leading multi-vendor e-commerce platform. Shop from thousands of verified sellers.</p>
                <div className="features">
                    <div className="feature">
                        <i className="fas fa-shield-alt"></i>
                        <div>
                            <h4>Secure Shopping</h4>
                            <p>100% secure payment processing</p>
                        </div>
                    </div>
                    <div className="feature">
                        <i className="fas fa-truck"></i>
                        <div>
                            <h4>Fast Delivery</h4>
                            <p>Free shipping on orders over ৳2000</p>
                        </div>
                    </div>
                    <div className="feature">
                        <i className="fas fa-undo"></i>
                        <div>
                            <h4>Easy Returns</h4>
                            <p>7 days hassle-free return policy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div className="auth-right">
            <div className="auth-form">
                <h2>Welcome Back!</h2>
                <p className="subtitle">Sign in to continue shopping</p>
                <form>
                    <div className="form-group">
                        <label htmlFor="email">Email Address</label>
                        <input type="email" id="email" name="email" className="form-control" placeholder="Enter your email" required autofocus />
                    </div>
                    <div className="form-group">
                        <label htmlFor="password">Password</label>
                        <input type="password" id="password" name="password" className="form-control" placeholder="Enter your password" required />
                    </div>
                    <div className="checkbox-group">
                        <label><input type="checkbox" name="remember" /> Remember me</label>
                        <a href="#">Forgot Password?</a>
                    </div>
                    <button type="submit" className="btn btn-primary">
                        Sign In <i className="fas fa-arrow-right ex-style-70"></i>
                    </button>
                </form>
                <div className="divider"><span>or continue with</span></div>
                <div className="social-login">
                    <a href="#" className="social-btn google" aria-label="Continue with Google"><i className="fab fa-google"></i></a>
                    <a href="#" className="social-btn facebook" aria-label="Continue with Facebook"><i className="fab fa-facebook-f"></i></a>
                </div>
                <p className="auth-footer">Don't have an account? <a href="register.html">Create Account</a></p>
            </div>
        </div>
    </div>
</>


    );
}
