import React, { useState, useEffect } from 'react';
import { Card, StatusBadge } from '../../components/Shared';
import { HostingPlan, User, Payment } from '../../types';
import { CreditCard, QrCode, Upload, Check, Loader2, X, AlertTriangle, ArrowRight, RefreshCcw, FileText, Clock } from 'lucide-react';
import { api } from '../../services/api';

interface BillingProps {
    plans: HostingPlan[];
    userPlanName?: string;
    user: User; // Added user prop to link payments
}

export const Billing: React.FC<BillingProps> = ({ plans = [], userPlanName = 'Basic', user }) => {
    const [selectedPlan, setSelectedPlan] = useState<HostingPlan | null>(null);
    const [uniqueCode, setUniqueCode] = useState(0);
    const [paymentMethod, setPaymentMethod] = useState<'BANK' | 'QR'>('BANK');
    const [proofFile, setProofFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [successMsg, setSuccessMsg] = useState('');
    
    // History State
    const [paymentHistory, setPaymentHistory] = useState<Payment[]>([]);

    useEffect(() => {
        loadHistory();
    }, [user.id, isSubmitting]);

    const loadHistory = async () => {
        const history = await api.billing.getHistory(user.id);
        setPaymentHistory(history);
    };

    const handleUpgradeClick = (plan: HostingPlan) => {
        // Generate random unique code between 0 and 500
        const code = Math.floor(Math.random() * 501);
        setUniqueCode(code);
        setSelectedPlan(plan);
        setPaymentMethod('BANK');
        setProofFile(null);
        setSuccessMsg('');
    };

    const handleCloseModal = () => {
        if (isSubmitting) return;
        setSelectedPlan(null);
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            setProofFile(e.target.files[0]);
        }
    };

    const handleSubmitPayment = async () => {
        if (!selectedPlan || !proofFile) return;
        
        setIsSubmitting(true);
        try {
            const totalAmount = selectedPlan.price + uniqueCode;
            await api.billing.submitPayment(user.id, user.username, selectedPlan.name, totalAmount, proofFile);
            
            setSuccessMsg("Payment submitted successfully! Please wait for admin verification.");
            setTimeout(() => {
                setSelectedPlan(null);
                setSuccessMsg('');
            }, 2500);
        } catch (e) {
            alert("Failed to submit payment.");
        } finally {
            setIsSubmitting(false);
        }
    };

    const totalAmount = selectedPlan ? selectedPlan.price + uniqueCode : 0;

    return (
        <div className="space-y-6 animate-in fade-in duration-300">
             <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {plans.map(plan => {
                    const isActive = plan.name === userPlanName;
                    const isFree = plan.price === 0;
                    return (
                        <div key={plan.id} className={`relative p-6 rounded-xl border-2 flex flex-col transition-all duration-300 ${isActive ? 'border-indigo-600 bg-white shadow-lg scale-[1.02]' : 'border-slate-200 bg-white hover:border-indigo-300'}`}>
                            {isActive && <div className="absolute top-0 right-0 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">CURRENT</div>}
                            {plan.isPopular && !isActive && <div className="absolute top-0 right-0 bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">POPULAR</div>}
                            
                            <h3 className="font-bold text-lg text-slate-900">{plan.name}</h3>
                            <div className="text-2xl font-bold text-slate-900 mt-2 mb-4">
                                {plan.price === 0 ? 'Free' : `${plan.currency} ${plan.price.toLocaleString()}`}
                                {plan.price > 0 && <span className="text-sm font-normal text-slate-500">/mo</span>}
                            </div>
                            
                            <ul className="space-y-3 mb-6 flex-1">
                                {plan.features.map((f, idx) => (
                                    <li key={idx} className="text-sm text-slate-600 flex items-start gap-2">
                                        <div className="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-1.5 shrink-0" /> {f}
                                    </li>
                                ))}
                            </ul>
                            
                            <button 
                                onClick={() => !isActive && !isFree && handleUpgradeClick(plan)}
                                disabled={isActive || isFree}
                                className={`w-full py-2 rounded-lg font-medium text-sm transition-colors mt-auto flex items-center justify-center gap-2
                                    ${isActive 
                                        ? 'bg-indigo-50 text-indigo-700 cursor-default' 
                                        : isFree 
                                            ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
                                            : 'bg-slate-900 text-white hover:bg-slate-800 shadow-md hover:shadow-lg'
                                    }`}
                            >
                                {isActive ? <><Check className="w-4 h-4" /> Current Plan</> : 'Upgrade Now'}
                            </button>
                        </div>
                    );
                })}
             </div>

             <Card title="Payment History">
                 {paymentHistory.length === 0 ? (
                    <div className="text-center py-12 flex flex-col items-center justify-center">
                        <div className="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                             <FileText className="w-8 h-8 text-slate-300" />
                        </div>
                        <h4 className="text-slate-900 font-bold mb-1">No Transactions</h4>
                        <p className="text-slate-500 text-sm">You haven't purchased any plans yet.</p>
                    </div>
                 ) : (
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-left text-sm whitespace-nowrap">
                            <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                                <tr>
                                    <th className="px-6 py-3 font-medium">Date</th>
                                    <th className="px-6 py-3 font-medium">Invoice ID</th>
                                    <th className="px-6 py-3 font-medium">Plan</th>
                                    <th className="px-6 py-3 font-medium">Method</th>
                                    <th className="px-6 py-3 font-medium">Amount</th>
                                    <th className="px-6 py-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-50">
                                {paymentHistory.map(pay => (
                                    <tr key={pay.id} className="hover:bg-slate-50/50 transition-colors">
                                        <td className="px-6 py-4 text-slate-600 flex items-center gap-2">
                                            <Clock className="w-4 h-4 text-slate-400" />
                                            {pay.date}
                                        </td>
                                        <td className="px-6 py-4 font-mono text-xs text-slate-500">#{pay.id}</td>
                                        <td className="px-6 py-4 font-bold text-slate-800">{pay.plan}</td>
                                        <td className="px-6 py-4">
                                            {pay.method === 'BANK' ? (
                                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">
                                                    <CreditCard className="w-3 h-3" /> Bank Transfer
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-orange-50 text-orange-700 text-xs font-medium border border-orange-100">
                                                    <QrCode className="w-3 h-3" /> QRIS / E-Wallet
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 font-medium text-slate-900">Rp {pay.amount.toLocaleString()}</td>
                                        <td className="px-6 py-4">
                                            <StatusBadge status={pay.status} />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                 )}
             </Card>

             {/* PAYMENT MODAL */}
             {selectedPlan && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center p-4">
                    <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={handleCloseModal} />
                    <div className="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                        
                        {/* Modal Header */}
                        <div className="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <div>
                                <h3 className="text-lg font-bold text-slate-900">Upgrade to {selectedPlan.name}</h3>
                                <p className="text-xs text-slate-500">Complete payment to activate.</p>
                            </div>
                            <button onClick={handleCloseModal} disabled={isSubmitting} className="p-2 hover:bg-slate-200 rounded-full text-slate-500 transition-colors">
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        {successMsg ? (
                            <div className="p-12 flex flex-col items-center text-center animate-in fade-in slide-in-from-bottom-4">
                                <div className="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-4">
                                    <Check className="w-8 h-8" />
                                </div>
                                <h4 className="text-xl font-bold text-slate-900 mb-2">Payment Submitted!</h4>
                                <p className="text-slate-500 text-sm">{successMsg}</p>
                            </div>
                        ) : (
                            <div className="p-6">
                                {/* WARNING ALERT for Plan Replacement */}
                                {userPlanName && userPlanName !== 'Basic' && userPlanName !== 'Free' && (
                                    <div className="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3 animate-in fade-in slide-in-from-top-2">
                                        <div className="p-2 bg-amber-100 rounded-full shrink-0">
                                            <RefreshCcw className="w-5 h-5 text-amber-600" />
                                        </div>
                                        <div>
                                            <h4 className="font-bold text-amber-800 text-sm">Plan Replacement Warning</h4>
                                            <p className="text-xs text-amber-700 mt-1 leading-relaxed">
                                                You are currently active on the <span className="font-bold">{userPlanName}</span> plan. 
                                                Proceeding with this upgrade will <span className="font-bold underline">replace/overwrite</span> your existing plan immediately. 
                                                Any remaining duration on your current plan will not be carried over.
                                            </p>
                                        </div>
                                    </div>
                                )}

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    {/* LEFT COLUMN: Summary & Options */}
                                    <div className="space-y-6">
                                        {/* Amount Display */}
                                        <div className="bg-indigo-50 border border-indigo-100 rounded-xl p-5 flex flex-col justify-between h-auto min-h-[120px]">
                                            <div>
                                                <p className="text-xs text-indigo-600 font-bold uppercase tracking-wider mb-2">Total Amount to Transfer</p>
                                                <div className="flex items-baseline gap-1 mb-2">
                                                    <span className="text-3xl font-bold text-indigo-900">Rp {totalAmount.toLocaleString()}</span>
                                                </div>
                                                <div className="flex items-center gap-2 text-xs text-indigo-500">
                                                    <span className="px-1.5 py-0.5 bg-indigo-100 rounded text-[10px] font-mono font-bold">{uniqueCode}</span>
                                                    <span>unique verification code included</span>
                                                </div>
                                            </div>
                                            <div className="mt-4 pt-3 border-t border-indigo-100">
                                                <div className="flex justify-between items-center">
                                                    <span className="text-xs text-slate-500">Plan Price:</span>
                                                    <span className="text-sm font-medium text-slate-700">Rp {selectedPlan.price.toLocaleString()}</span>
                                                </div>
                                                <div className="flex justify-between items-center mt-1">
                                                    <span className="text-xs text-slate-500">Unique Code:</span>
                                                    <span className="text-sm font-medium text-slate-700">Rp {uniqueCode}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="p-3 bg-amber-50 text-amber-800 text-xs rounded-lg border border-amber-100 flex gap-2">
                                            <AlertTriangle className="w-4 h-4 shrink-0 mt-0.5" />
                                            <p>Important: Please transfer the <b>EXACT</b> amount (down to the last 3 digits) for automatic verification.</p>
                                        </div>

                                        {/* Payment Method Tabs */}
                                        <div>
                                            <label className="text-sm font-medium text-slate-700 mb-2 block">Select Payment Method</label>
                                            <div className="grid grid-cols-2 gap-3">
                                                <button 
                                                    onClick={() => setPaymentMethod('BANK')}
                                                    className={`p-3 rounded-xl border flex flex-col items-center gap-2 transition-all ${paymentMethod === 'BANK' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600' : 'border-slate-200 hover:border-slate-300 text-slate-600'}`}
                                                >
                                                    <CreditCard className="w-6 h-6" />
                                                    <span className="text-sm font-bold">Bank Transfer</span>
                                                </button>
                                                <button 
                                                    onClick={() => setPaymentMethod('QR')}
                                                    className={`p-3 rounded-xl border flex flex-col items-center gap-2 transition-all ${paymentMethod === 'QR' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600' : 'border-slate-200 hover:border-slate-300 text-slate-600'}`}
                                                >
                                                    <QrCode className="w-6 h-6" />
                                                    <span className="text-sm font-bold">QRIS / E-Wallet</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {/* RIGHT COLUMN: Details & Action */}
                                    <div className="space-y-6 flex flex-col">
                                        {/* Method Details */}
                                        <div className="bg-slate-50 rounded-xl p-5 border border-slate-200 flex-1">
                                            {paymentMethod === 'BANK' ? (
                                                <div className="space-y-5 h-full flex flex-col justify-center">
                                                    <div className="flex items-center justify-between border-b border-slate-200 pb-4">
                                                        <div className="flex items-center gap-3">
                                                            <img src="https://play-lh.googleusercontent.com/sykVTkZ6juW7CD1eeZCK1UTi1aDwr4tOQ6KRMuMimOsIZYsK9Rbxwhk-PGu3nA1iaoQ1=w240-h480-rw" alt="Bank Mandiri" className="w-12 h-12 rounded-lg object-contain bg-white shadow-sm border border-slate-100" />
                                                            <div>
                                                                <p className="text-xs text-slate-500 font-medium">Bank Mandiri</p>
                                                                <p className="font-bold text-slate-900 text-sm">ANDI AHMAD NURMADANI</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p className="text-xs text-slate-500 mb-1.5 uppercase tracking-wide font-bold">Account Number</p>
                                                        <div className="flex items-center justify-between bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                                                            <code className="text-xl font-mono font-bold text-slate-800 tracking-wider">1770020697923</code>
                                                            <button className="text-xs text-indigo-600 font-bold hover:bg-indigo-50 px-2 py-1 rounded transition-colors" onClick={() => navigator.clipboard.writeText('1770020697923')}>COPY</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            ) : (
                                                <div className="flex flex-col items-center text-center justify-center h-full">
                                                    <div className="bg-white p-3 rounded-xl border border-slate-200 mb-4 shadow-sm relative overflow-hidden">
                                                         <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=1770020697923" alt="QRIS" className="w-40 h-40 mix-blend-multiply" />
                                                    </div>
                                                    <p className="font-bold text-slate-900 text-sm">HOSTING KOLAB</p>
                                                    <p className="text-xs text-slate-500 font-medium">BARANG DIGITAL</p>
                                                    <p className="text-[10px] text-slate-400 mt-1 font-mono">NMID : ID1025465066435</p>
                                                </div>
                                            )}
                                        </div>

                                        {/* Proof Upload & Button */}
                                        <div className="pt-2">
                                            <div className="mb-4">
                                                <label className="text-sm font-medium text-slate-700 mb-2 block">Upload Payment Proof</label>
                                                <div className="relative">
                                                    <input 
                                                        type="file" 
                                                        accept="image/*"
                                                        onChange={handleFileChange}
                                                        className="hidden" 
                                                        id="proof-upload"
                                                    />
                                                    <label 
                                                        htmlFor="proof-upload"
                                                        className={`flex items-center justify-center gap-2 w-full p-3 rounded-lg border-2 border-dashed cursor-pointer transition-colors ${proofFile ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-300 hover:border-indigo-500 hover:bg-slate-50 text-slate-500'}`}
                                                    >
                                                        {proofFile ? (
                                                            <><Check className="w-4 h-4" /> <span className="text-sm font-medium truncate">{proofFile.name}</span></>
                                                        ) : (
                                                            <><Upload className="w-4 h-4" /> <span className="text-sm font-medium">Click to upload screenshot</span></>
                                                        )}
                                                    </label>
                                                </div>
                                            </div>

                                            <div className="flex gap-3">
                                                <button onClick={handleCloseModal} className="px-4 py-2.5 text-slate-600 font-medium text-sm hover:bg-slate-100 rounded-lg">
                                                    Cancel
                                                </button>
                                                <button 
                                                    onClick={handleSubmitPayment} 
                                                    disabled={!proofFile || isSubmitting}
                                                    className="flex-1 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md flex items-center justify-center gap-2"
                                                >
                                                    {isSubmitting ? <Loader2 className="w-4 h-4 animate-spin" /> : <>Confirm Payment <ArrowRight className="w-4 h-4" /></>}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
             )}
        </div>
    )
}