import React, { useEffect, useState } from 'react';
import { CheckCircle, XCircle, Loader2, Database, Key } from 'lucide-react';

interface VerifyEmailPageProps {
    token: string;
    onNavigateToLogin: () => void;
}

export const VerifyEmailPage: React.FC<VerifyEmailPageProps> = ({ token, onNavigateToLogin }) => {
    const [status, setStatus] = useState<'loading' | 'success' | 'error'>('loading');
    const [message, setMessage] = useState('');
    const [credentials, setCredentials] = useState<any>(null);

    useEffect(() => {
        // If no token (e.g., user refreshed after URL was cleaned), show error immediately
        if (!token || token.trim() === '') {
            setStatus('error');
            setMessage('No verification token found. Please use the link from your email.');
            return;
        }
        verifyEmail();
    }, [token]);

    const verifyEmail = async () => {
        try {
            const response = await fetch(`http://localhost:5000/api/auth/verify-email?token=${token}`);
            const data = await response.json();

            if (response.ok) {
                setStatus('success');
                setMessage(data.message);
                setCredentials(data.mysqlCredentials);

                // Clean URL to remove token parameter
                window.history.replaceState({}, '', window.location.pathname);

                // Auto-redirect to login after 10 seconds
                setTimeout(() => {
                    onNavigateToLogin();
                }, 10000);
            } else {
                setStatus('error');
                setMessage(data.message || 'Verification failed');
            }
        } catch (err: any) {
            setStatus('error');
            setMessage('Network error. Please try again.');
        }
    };

    if (status === 'loading') {
        return (
            <div className="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center p-4">
                <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 text-center">
                    <Loader2 className="w-16 h-16 text-indigo-600 mx-auto mb-4 animate-spin" />
                    <h2 className="text-2xl font-bold text-slate-900 mb-2">Verifying Your Email...</h2>
                    <p className="text-slate-600">Please wait while we activate your account</p>
                </div>
            </div>
        );
    }

    if (status === 'error') {
        return (
            <div className="min-h-screen bg-gradient-to-br from-red-500 via-pink-500 to-orange-500 flex items-center justify-center p-4">
                <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 text-center">
                    <XCircle className="w-16 h-16 text-red-500 mx-auto mb-4" />
                    <h2 className="text-2xl font-bold text-slate-900 mb-2">Verification Failed</h2>
                    <p className="text-slate-600 mb-6">{message}</p>

                    <button
                        onClick={onNavigateToLogin}
                        className="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
                    >
                        Go to Login
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gradient-to-br from-green-500 via-emerald-500 to-teal-500 flex items-center justify-center p-4">
            <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
                <div className="text-center">
                    <CheckCircle className="w-16 h-16 text-green-500 mx-auto mb-4" />
                    <h2 className="text-2xl font-bold text-slate-900 mb-2">Email Verified! 🎉</h2>
                    <p className="text-slate-600 mb-6">{message}</p>

                    {credentials && (
                        <div className="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-lg p-6 mb-6 text-left">
                            <div className="flex items-center gap-2 mb-4">
                                <Database className="w-5 h-5 text-indigo-600" />
                                <h3 className="font-bold text-indigo-900">Your phpMyAdmin Access</h3>
                            </div>

                            <div className="space-y-3 text-sm">
                                <div className="flex items-start gap-2">
                                    <Key className="w-4 h-4 text-slate-500 mt-0.5" />
                                    <div className="flex-1">
                                        <p className="text-slate-600 font-medium">Username:</p>
                                        <p className="text-slate-900 font-mono bg-white px-2 py-1 rounded border border-slate-200">
                                            {credentials.username}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-2">
                                    <Key className="w-4 h-4 text-slate-500 mt-0.5" />
                                    <div className="flex-1">
                                        <p className="text-slate-600 font-medium">Password:</p>
                                        <p className="text-slate-900 font-mono bg-white px-2 py-1 rounded border border-slate-200 break-all">
                                            {credentials.password}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-2">
                                    <Database className="w-4 h-4 text-slate-500 mt-0.5" />
                                    <div className="flex-1">
                                        <p className="text-slate-600 font-medium">Database:</p>
                                        <p className="text-slate-900 font-mono bg-white px-2 py-1 rounded border border-slate-200">
                                            {credentials.database}
                                        </p>
                                    </div>
                                </div>

                                <div className="bg-green-50 border border-green-200 rounded p-3 mt-4">
                                    <p className="text-xs text-green-800">
                                        <strong>✅ Credentials also sent to your email</strong> for your records.
                                    </p>
                                </div>
                            </div>
                        </div>
                    )}

                    <button
                        onClick={onNavigateToLogin}
                        className="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
                    >
                        Login to KolabPanel
                    </button>
                </div>
            </div>
        </div>
    );
};
