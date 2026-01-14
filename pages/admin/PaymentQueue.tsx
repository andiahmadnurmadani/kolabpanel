import React, { useState, useEffect } from 'react';
import { Card } from '../../components/Shared';
import { api } from '../../services/api';
import { Payment, PaymentStatus, User } from '../../types';
import { CheckCircle, XCircle, Eye, FileImage, X, ZoomIn, CreditCard, QrCode } from 'lucide-react';

export const PaymentQueue: React.FC = () => {
    const [payments, setPayments] = useState<Payment[]>([]);
    const [viewingProof, setViewingProof] = useState<Payment | null>(null);

    useEffect(() => {
        loadPayments();
    }, []);

    const loadPayments = () => {
        api.admin.getPayments().then(setPayments);
    };

    const handleVerify = async (id: string, status: PaymentStatus) => {
        await api.admin.verifyPayment(id, status);
        loadPayments();
        if (viewingProof?.id === id) setViewingProof(null);
    };

    // Helper to handle mock urls vs real urls
    const getImageUrl = (url: string) => {
        if (url === 'mock_proof_url.jpg') {
            // Return a realistic looking receipt placeholder for the mock
            return "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?q=80&w=1000&auto=format&fit=crop";
        }
        return url; // In real app, this would be the uploaded file URL
    };

    return (
        <>
            <Card title="Payment Verification Queue">
                <div className="space-y-4">
                    {payments.length === 0 ? (
                        <div className="text-center py-8 text-slate-500">No pending payments.</div>
                    ) : (
                        payments.map(pay => (
                            <div key={pay.id} className="flex flex-col md:flex-row items-center justify-between p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors group">
                                <div className="flex items-center gap-4 mb-4 md:mb-0 w-full md:w-auto">
                                    <div className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold shrink-0">
                                        {pay.username.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <h4 className="font-semibold text-slate-900">{pay.username}</h4>
                                            <span className="text-[10px] font-mono bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200" title="Invoice ID">
                                                #{pay.id}
                                            </span>
                                        </div>
                                        <p className="text-sm text-slate-500">Requesting <span className="font-bold text-indigo-600">{pay.plan}</span> Plan</p>
                                        <div className="flex items-center gap-2 mt-1">
                                             <span className={`text-xs font-bold px-2 py-0.5 rounded ${pay.status === 'PENDING' ? 'bg-amber-100 text-amber-700' : pay.status === 'VERIFIED' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}`}>{pay.status}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                                    <div className="flex flex-col items-end">
                                        <p className="font-bold text-slate-900 text-lg">Rp {pay.amount.toLocaleString()}</p>
                                        <div className="flex items-center gap-2 text-xs text-slate-500 mb-1">
                                            {pay.method === 'BANK' ? (
                                                <span className="flex items-center gap-1 bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-100"><CreditCard className="w-3 h-3" /> Bank Transfer</span>
                                            ) : (
                                                <span className="flex items-center gap-1 bg-orange-50 text-orange-700 px-1.5 py-0.5 rounded border border-orange-100"><QrCode className="w-3 h-3" /> QRIS / Wallet</span>
                                            )}
                                            <span>•</span>
                                            <span>{pay.date}</span>
                                        </div>
                                    </div>
                                    
                                    <div className="flex gap-2">
                                        <button 
                                            onClick={() => setViewingProof(pay)}
                                            className="p-2 text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors"
                                            title="View Proof"
                                        >
                                            <Eye className="w-5 h-5" />
                                        </button>

                                        {pay.status === 'PENDING' && (
                                            <>
                                                <div className="w-px h-8 bg-slate-200 mx-1"></div>
                                                <button onClick={() => handleVerify(pay.id, PaymentStatus.VERIFIED)} className="p-2 text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors" title="Approve">
                                                    <CheckCircle className="w-5 h-5" />
                                                </button>
                                                <button onClick={() => handleVerify(pay.id, PaymentStatus.REJECTED)} className="p-2 text-rose-600 bg-rose-50 rounded-lg hover:bg-rose-100 transition-colors" title="Reject">
                                                    <XCircle className="w-5 h-5" />
                                                </button>
                                            </>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </Card>

            {/* Proof Viewer Modal */}
            {viewingProof && (
                <div className="fixed inset-0 z-[80] flex items-center justify-center p-4">
                    <div className="absolute inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" onClick={() => setViewingProof(null)} />
                    <div className="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
                        <div className="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <div>
                                <h3 className="font-bold text-slate-800 flex items-center gap-2">
                                    <FileImage className="w-4 h-4 text-indigo-600" /> Payment Proof
                                </h3>
                                <p className="text-xs text-slate-500">
                                    Uploaded by {viewingProof.username} <span className="mx-1">•</span> <span className="font-mono">#{viewingProof.id}</span>
                                </p>
                            </div>
                            <button onClick={() => setViewingProof(null)} className="p-1.5 hover:bg-slate-200 rounded-full text-slate-400 transition-colors">
                                <X className="w-5 h-5" />
                            </button>
                        </div>
                        
                        <div className="p-1 bg-slate-200 overflow-y-auto flex-1 flex items-center justify-center min-h-[300px]">
                            <img 
                                src={getImageUrl(viewingProof.proofUrl)} 
                                alt="Proof" 
                                className="max-w-full h-auto object-contain shadow-sm rounded-sm" 
                            />
                        </div>

                        {viewingProof.status === 'PENDING' && (
                            <div className="p-4 bg-white border-t border-slate-100 flex gap-3">
                                <button 
                                    onClick={() => handleVerify(viewingProof.id, PaymentStatus.REJECTED)} 
                                    className="flex-1 py-2 text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 rounded-lg font-bold text-sm transition-colors"
                                >
                                    Reject
                                </button>
                                <button 
                                    onClick={() => handleVerify(viewingProof.id, PaymentStatus.VERIFIED)} 
                                    className="flex-[2] py-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg font-bold text-sm shadow-md transition-colors"
                                >
                                    Verify Payment
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            )}
        </>
    )
}