import React, { useState, useEffect } from 'react';
import { Card } from '../../components/Shared';
import { api } from '../../services/api';
import { Payment, PaymentStatus, User } from '../../types';
import { CheckCircle, XCircle } from 'lucide-react';

export const PaymentQueue: React.FC = () => {
    const [payments, setPayments] = useState<Payment[]>([]);

    useEffect(() => {
        loadPayments();
    }, []);

    const loadPayments = () => {
        api.admin.getPayments().then(setPayments);
    };

    const handleVerify = async (id: string, status: PaymentStatus) => {
        await api.admin.verifyPayment(id, status);
        loadPayments();
    };

    return (
        <Card title="Payment Verification Queue">
            <div className="space-y-4">
                {payments.length === 0 ? (
                    <div className="text-center py-8 text-slate-500">No pending payments.</div>
                ) : (
                    payments.map(pay => (
                        <div key={pay.id} className="flex flex-col md:flex-row items-center justify-between p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                            <div className="flex items-center gap-4 mb-4 md:mb-0">
                                <div className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold">
                                    {pay.username.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <h4 className="font-semibold text-slate-900">{pay.username}</h4>
                                    <p className="text-sm text-slate-500">Requesting <span className="font-bold text-indigo-600">{pay.plan}</span> Plan</p>
                                    <span className={`text-xs font-bold px-2 py-0.5 rounded ${pay.status === 'PENDING' ? 'bg-amber-100 text-amber-700' : pay.status === 'VERIFIED' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}`}>{pay.status}</span>
                                </div>
                            </div>
                            
                            <div className="flex items-center gap-6">
                                <div className="text-right">
                                    <p className="font-bold text-slate-900">Rp {pay.amount.toLocaleString()}</p>
                                    <p className="text-xs text-slate-500">{pay.date}</p>
                                </div>
                                {pay.status === 'PENDING' && (
                                    <div className="flex gap-2">
                                        <button onClick={() => handleVerify(pay.id, PaymentStatus.VERIFIED)} className="p-2 text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100" title="Approve">
                                            <CheckCircle className="w-5 h-5" />
                                        </button>
                                        <button onClick={() => handleVerify(pay.id, PaymentStatus.REJECTED)} className="p-2 text-rose-600 bg-rose-50 rounded-lg hover:bg-rose-100" title="Reject">
                                            <XCircle className="w-5 h-5" />
                                        </button>
                                    </div>
                                )}
                            </div>
                        </div>
                    ))
                )}
            </div>
        </Card>
    )
}
