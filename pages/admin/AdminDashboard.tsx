import React, { useState, useEffect } from 'react';
import { Card } from '../../components/Shared';
import { api } from '../../services/api';
import { Users, DollarSign, Activity, CheckCircle, Server, TrendingUp, CreditCard, Globe, ArrowUpRight, ArrowDownRight, Clock } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, AreaChart, Area } from 'recharts';

export const AdminDashboard: React.FC = () => {
  const [stats, setStats] = useState({ totalUsers: 0, totalSites: 0, activeRevenue: '0' });
  const [loading, setLoading] = useState(true);

  // Mock data for charts - in a real app this would come from an analytics endpoint
  const revenueData = [
    { name: 'Mon', income: 2400 },
    { name: 'Tue', income: 1398 },
    { name: 'Wed', income: 9800 },
    { name: 'Thu', income: 3908 },
    { name: 'Fri', income: 4800 },
    { name: 'Sat', income: 3800 },
    { name: 'Sun', income: 4300 },
  ];

  const trafficData = [
    { name: '00:00', visits: 120 },
    { name: '04:00', visits: 80 },
    { name: '08:00', visits: 450 },
    { name: '12:00', visits: 980 },
    { name: '16:00', visits: 850 },
    { name: '20:00', visits: 600 },
    { name: '23:59', visits: 300 },
  ];

  useEffect(() => {
     const loadStats = async () => {
         try {
             const data = await api.admin.getStats();
             setStats(data);
         } catch (e) {
             console.error("Failed to load stats");
         } finally {
             setLoading(false);
         }
     };
     loadStats();
  }, []);

  const StatWidget = ({ 
    title, 
    value, 
    icon: Icon, 
    gradient, 
    trend, 
    trendUp, 
    subLabel 
  }: { 
    title: string, 
    value: string | number, 
    icon: any, 
    gradient: string, 
    trend?: string, 
    trendUp?: boolean,
    subLabel?: string
  }) => (
    <div className={`relative overflow-hidden rounded-2xl p-6 text-white shadow-lg transition-transform hover:scale-[1.02] duration-300 ${gradient}`}>
        {/* Background Decoration */}
        <div className="absolute -right-6 -bottom-6 opacity-10 rotate-12">
            <Icon className="w-32 h-32" />
        </div>
        
        <div className="relative z-10">
            <div className="flex justify-between items-start mb-4">
                <div className="p-2.5 bg-white/20 backdrop-blur-sm rounded-xl border border-white/10 shadow-inner">
                    <Icon className="w-6 h-6 text-white" />
                </div>
                {trend && (
                    <div className={`flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold backdrop-blur-md border border-white/10 ${trendUp ? 'bg-emerald-400/30 text-emerald-50' : 'bg-red-400/30 text-red-50'}`}>
                        {trendUp ? <ArrowUpRight className="w-3 h-3" /> : <ArrowDownRight className="w-3 h-3" />}
                        {trend}
                    </div>
                )}
            </div>
            
            <div className="space-y-1">
                <h3 className="text-indigo-100 text-sm font-medium tracking-wide opacity-90">{title}</h3>
                <div className="text-3xl font-bold tracking-tight">{value}</div>
                {subLabel && <p className="text-xs text-white/60 font-medium">{subLabel}</p>}
            </div>
        </div>
    </div>
  );

  return (
    <div className="space-y-8 animate-in fade-in duration-500 pb-10">
      
      {/* Header Section */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
              <h1 className="text-2xl font-bold text-slate-900">Dashboard Overview</h1>
              <p className="text-slate-500 text-sm mt-1">Welcome back, Administrator. Here's what's happening today.</p>
          </div>
          <div className="flex items-center gap-2 text-sm text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
              <Clock className="w-4 h-4" />
              <span>Last updated: {new Date().toLocaleTimeString()}</span>
          </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatWidget 
            title="Total Revenue" 
            value={`Rp ${stats.activeRevenue}`} 
            icon={DollarSign} 
            gradient="bg-gradient-to-br from-indigo-600 to-violet-600"
            trend="+12.5%"
            trendUp={true}
            subLabel="Monthly Recurring"
        />
        <StatWidget 
            title="Active Users" 
            value={stats.totalUsers} 
            icon={Users} 
            gradient="bg-gradient-to-br from-blue-500 to-cyan-500"
            trend="+3 New"
            trendUp={true}
            subLabel="Total Registered"
        />
        <StatWidget 
            title="Deployed Sites" 
            value={stats.totalSites} 
            icon={Globe} 
            gradient="bg-gradient-to-br from-emerald-500 to-teal-500"
            trend="+5%"
            trendUp={true}
            subLabel="Across all nodes"
        />
        <StatWidget 
            title="System Load" 
            value="12%" 
            icon={Activity} 
            gradient="bg-gradient-to-br from-orange-500 to-rose-500"
            trend="-2%"
            trendUp={false} // Good that it's down
            subLabel="CPU Usage Avg"
        />
      </div>

      {/* Charts Section */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Revenue Chart */}
        <div className="lg:col-span-2">
            <Card className="h-full border-0 shadow-md ring-1 ring-slate-200/60">
                <div className="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 className="font-bold text-slate-800 flex items-center gap-2">
                            <TrendingUp className="w-5 h-5 text-indigo-600" /> Revenue Analytics
                        </h3>
                        <p className="text-xs text-slate-500 mt-0.5">Income trends over the last 7 days</p>
                    </div>
                    <select className="text-xs border-none bg-slate-50 rounded-lg px-2 py-1 text-slate-600 outline-none cursor-pointer hover:bg-slate-100 transition-colors">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
                <div className="p-6 h-[320px]">
                    <ResponsiveContainer width="100%" height="100%">
                        <AreaChart data={revenueData}>
                            <defs>
                                <linearGradient id="colorIncome" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="5%" stopColor="#4f46e5" stopOpacity={0.2}/>
                                    <stop offset="95%" stopColor="#4f46e5" stopOpacity={0}/>
                                </linearGradient>
                            </defs>
                            <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#e2e8f0" />
                            <XAxis dataKey="name" tick={{fontSize: 12, fill: '#64748b'}} axisLine={false} tickLine={false} dy={10} />
                            <YAxis tick={{fontSize: 12, fill: '#64748b'}} axisLine={false} tickLine={false} tickFormatter={(val) => `Rp${val/1000}k`} />
                            <Tooltip 
                                contentStyle={{backgroundColor: '#fff', borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)'}} 
                                itemStyle={{color: '#4338ca', fontWeight: 'bold'}}
                            />
                            <Area type="monotone" dataKey="income" stroke="#4f46e5" strokeWidth={3} fillOpacity={1} fill="url(#colorIncome)" />
                        </AreaChart>
                    </ResponsiveContainer>
                </div>
            </Card>
        </div>

        {/* Traffic/Server Stats */}
        <div className="lg:col-span-1">
             <Card className="h-full border-0 shadow-md ring-1 ring-slate-200/60">
                <div className="px-6 py-4 border-b border-slate-100">
                    <h3 className="font-bold text-slate-800 flex items-center gap-2">
                        <Server className="w-5 h-5 text-emerald-600" /> Real-time Traffic
                    </h3>
                     <p className="text-xs text-slate-500 mt-0.5">Requests per hour (Localhost Tunnel)</p>
                </div>
                <div className="p-6 h-[320px]">
                    <ResponsiveContainer width="100%" height="100%">
                        <BarChart data={trafficData}>
                            <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#e2e8f0" />
                            <XAxis dataKey="name" tick={{fontSize: 10, fill: '#64748b'}} axisLine={false} tickLine={false} />
                            <Tooltip cursor={{fill: '#f1f5f9'}} contentStyle={{borderRadius: '8px', border: 'none', boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)'}} />
                            <Bar dataKey="visits" fill="#10b981" radius={[4, 4, 0, 0]} barSize={20} />
                        </BarChart>
                    </ResponsiveContainer>
                </div>
             </Card>
        </div>
      </div>

      {/* Recent Activity Mini-Section */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div className="p-3 bg-indigo-50 text-indigo-600 rounded-full">
                    <CreditCard className="w-6 h-6" />
                </div>
                <div>
                    <p className="text-sm font-bold text-slate-800">Pending Payments</p>
                    <p className="text-xs text-slate-500">4 transactions need review</p>
                </div>
                <div className="ml-auto">
                    <span className="flex h-3 w-3 relative">
                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span className="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                </div>
            </div>
             <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div className="p-3 bg-blue-50 text-blue-600 rounded-full">
                    <Server className="w-6 h-6" />
                </div>
                <div>
                    <p className="text-sm font-bold text-slate-800">Node Status</p>
                    <p className="text-xs text-slate-500">All systems operational</p>
                </div>
                <div className="ml-auto text-emerald-500">
                    <CheckCircle className="w-5 h-5" />
                </div>
            </div>
             <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div className="p-3 bg-amber-50 text-amber-600 rounded-full">
                    <Activity className="w-6 h-6" />
                </div>
                <div>
                    <p className="text-sm font-bold text-slate-800">Disk Usage</p>
                    <p className="text-xs text-slate-500">450GB / 1TB (45%)</p>
                </div>
            </div>
      </div>
    </div>
  );
};