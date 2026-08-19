@extends('layouts.app')

@section('content')
    <div class="container" style="text-align: center; padding: 100px 20px;">
        <div
            style="width: 100px; height: 100px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <i class="fas fa-clock" style="font-size: 48px; color: #d97706;"></i>
        </div>

        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 12px; color: #d97706;">Vendor Application Pending</h1>
        <p
            style="color: #6b7280; font-size: 18px; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
            Your vendor application is currently under review. We'll notify you once your account has been approved.
        </p>

        <div class="card" style="max-width: 600px; margin: 0 auto; padding: 30px; text-align: left;">
            <h3 style="font-weight: 600; margin-bottom: 20px;">What happens next?</h3>

            <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                <div
                    style="width: 32px; height: 32px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #16a34a; flex-shrink: 0;">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p style="font-weight: 500;">Application Submitted</p>
                    <p style="font-size: 14px; color: #6b7280;">Your vendor application has been received</p>
                </div>
            </div>

            <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                <div
                    style="width: 32px; height: 32px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #d97706; flex-shrink: 0;">
                    <i class="fas fa-search"></i>
                </div>
                <div>
                    <p style="font-weight: 500;">Under Review</p>
                    <p style="font-size: 14px; color: #6b7280;">Our team is reviewing your application</p>
                </div>
            </div>

            <div style="display: flex; gap: 16px;">
                <div
                    style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #94a3b8; flex-shrink: 0;">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <p style="font-weight: 500; color: #94a3b8;">Account Activation</p>
                    <p style="font-size: 14px; color: #94a3b8;">Once approved, you can start selling</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
@endsection