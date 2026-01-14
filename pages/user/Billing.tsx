import React from 'react';
import { Card } from '../../components/Shared';
import { HostingPlan } from '../../types';

interface BillingProps {
    plans: HostingPlan[];
    userPlanName?: string;
}

export const Billing: React.FC<BillingProps> = ({ plans = [], userPlanName = 'Basic' }) => {
    return (
        <div className="space-y-6">
             <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {plans.map(plan => {
                    const isActive = plan.name === userPlanName;
                    return (
                        <div key={plan.id} className={`relative p-6 rounded-xl border-2 flex flex-col ${isActive ? 'border-indigo-600 bg-white shadow-lg' : 'border-slate-200 bg-white'}`}>
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
                            <button className={`w-full py-2 rounded-lg font-medium text-sm transition-colors mt-auto ${isActive ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-900 text-white hover:bg-slate-800'}`}>
                                {isActive ? 'Current Plan' : 'Upgrade'}
                            </button>
                        </div>
                    );
                })}
             </div>
             <Card title="Payment History">
                 <div className="text-center py-8 text-slate-500 text-sm">No recent transactions found.</div>
             </Card>
        </div>
    )
}
