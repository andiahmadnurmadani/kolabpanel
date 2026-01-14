import React, { useState, useEffect } from 'react';
import { Card, StatCard } from '../../components/Shared';
import { api } from '../../services/api';
import { Users, DollarSign, Activity, CheckCircle } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LineChart, Line } from 'recharts';

export const AdminDashboard: React.FC = () => {
  const [stats, setStats] = useState({ totalUsers: 0, totalSites: 0, activeRevenue: '0' });
  const [data, setData] = useState<any[]>([]);

  useEffect(() => {
     api.admin.getStats().then(setStats);
     // Keeping chart data visual-only for now, but stats above are real
     setData([
        { name: 'Mon', visits: 4000, income: 2400 },
        { name: 'Tue', visits: 3000, income: 1398 },
        { name: 'Wed', visits: 2000, income: 9800 },
        { name: 'Thu', visits: 2780, income: 3908 },
        { name: 'Fri', visits: 1890, income: 4800 },
        { name: 'Sat', visits: 2390, income: 3800 },
        { name: 'Sun', visits: 3490, income: 4300 },
      ]);
  }, []);

  return (
    <div className="space-y-6 animate-in fade-in duration-300">
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <StatCard title="Total Users" value={stats.totalUsers} icon={Users} trend="+12" trendUp={true} />
        <StatCard title="Active Revenue" value={stats.activeRevenue} icon={DollarSign} trend="+8%" trendUp={true} />
        <StatCard title="Total Sites" value={stats.totalSites} icon={Activity} />
        <StatCard title="Pending Review" value={4} icon={CheckCircle} trend="Action Needed" trendUp={false} />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card title="Revenue Overview">
          <div className="h-64">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={data}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} />
                <XAxis dataKey="name" tick={{fontSize: 12}} />
                <YAxis tick={{fontSize: 12}} />
                <Tooltip />
                <Bar dataKey="income" fill="#4f46e5" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </Card>
         <Card title="Traffic Stats">
          <div className="h-64">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={data}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} />
                <XAxis dataKey="name" tick={{fontSize: 12}} />
                <YAxis tick={{fontSize: 12}} />
                <Tooltip />
                <Line type="monotone" dataKey="visits" stroke="#0ea5e9" strokeWidth={2} />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </Card>
      </div>
    </div>
  );
};
