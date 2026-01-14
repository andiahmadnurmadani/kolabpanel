import React, { useState, useEffect, useRef } from 'react';
import { Card, StatusBadge } from '../../components/Shared';
import { api } from '../../services/api';
import { User, SupportTicket, ChatMessage } from '../../types';
import { Plus, Send, MessageSquare, Lock, Clock } from 'lucide-react';

interface SupportCenterProps {
    user: User;
}

export const SupportCenter: React.FC<SupportCenterProps> = ({ user }) => {
    const [tickets, setTickets] = useState<SupportTicket[]>([]);
    const [selectedTicketId, setSelectedTicketId] = useState<string | null>(null);
    const [messages, setMessages] = useState<ChatMessage[]>([]);
    const [newMessage, setNewMessage] = useState('');
    const [isCreating, setIsCreating] = useState(false);
    const [newSubject, setNewSubject] = useState('');
    const chatEndRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if(user?.id) loadTickets();
    }, [user?.id]);

    useEffect(() => {
        if (selectedTicketId) loadMessages(selectedTicketId);
    }, [selectedTicketId]);

    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    const loadTickets = async () => {
        if (!user?.id) return;
        const data = await api.tickets.list(user.id);
        setTickets(data);
    };

    const loadMessages = async (ticketId: string) => {
        const data = await api.tickets.getMessages(ticketId);
        setMessages(data);
    };

    const handleCreateTicket = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!newSubject.trim()) return;
        const ticket = await api.tickets.create(user.id, user.username, newSubject);
        setTickets([ticket, ...tickets]);
        setNewSubject('');
        setIsCreating(false);
        setSelectedTicketId(ticket.id);
    };

    const handleSendMessage = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!newMessage.trim() || !selectedTicketId) return;
        const sent = await api.tickets.sendMessage(selectedTicketId, user.id, user.username, newMessage, false);
        setMessages([...messages, sent]);
        setNewMessage('');
    };

    const selectedTicket = tickets.find(t => t.id === selectedTicketId);

     // Helper to get class for ticket item based on status & selection
    const getTicketItemClass = (ticket: SupportTicket, isSelected: boolean) => {
        const baseClass = "p-3 rounded-lg border cursor-pointer transition-all";
        
        if (isSelected) {
            return `${baseClass} bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200 shadow-sm`;
        }
        
        if (ticket.status === 'CLOSED') {
            return `${baseClass} bg-slate-50 border-slate-100 opacity-70 hover:opacity-100 hover:border-slate-300 grayscale-[0.3]`;
        }
        
        return `${baseClass} bg-white border-slate-200 hover:border-indigo-300 hover:shadow-sm`;
    };

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 h-[calc(100vh-140px)] animate-in fade-in duration-300">
            {/* Ticket List */}
            <div className="md:col-span-1 flex flex-col gap-4">
                 <Card className="h-full flex flex-col">
                     <div className="flex justify-between items-center mb-4">
                         <h3 className="font-bold text-slate-800">My Tickets</h3>
                         <button onClick={() => setIsCreating(true)} className="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"><Plus className="w-4 h-4" /></button>
                     </div>
                     {isCreating && (
                         <form onSubmit={handleCreateTicket} className="mb-4 p-3 bg-white rounded-lg border border-indigo-200 shadow-sm animate-in slide-in-from-top-2">
                             <input type="text" value={newSubject} onChange={e => setNewSubject(e.target.value)} placeholder="Subject..." className="w-full mb-2 px-3 py-2 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 outline-none" autoFocus />
                             <div className="flex justify-end gap-2">
                                 <button type="button" onClick={() => setIsCreating(false)} className="text-xs text-slate-500 hover:text-slate-800 font-medium px-2 py-1">Cancel</button>
                                 <button type="submit" className="text-xs bg-indigo-600 text-white px-3 py-1 rounded font-medium shadow-sm hover:bg-indigo-700">Create Ticket</button>
                             </div>
                         </form>
                     )}
                     <div className="flex-1 overflow-y-auto pr-2 space-y-2">
                         {tickets.map(ticket => (
                             <div 
                                key={ticket.id} 
                                onClick={() => setSelectedTicketId(ticket.id)} 
                                className={getTicketItemClass(ticket, selectedTicketId === ticket.id)}
                             >
                                 <div className="flex justify-between items-start mb-1">
                                     <span className={`font-medium text-sm truncate pr-2 ${ticket.status === 'CLOSED' ? 'text-slate-500' : 'text-slate-800'}`}>{ticket.subject}</span>
                                     <StatusBadge status={ticket.status} />
                                 </div>
                                 <div className="flex justify-between items-center text-xs text-slate-400">
                                     <span className="flex items-center gap-1"><Clock className="w-3 h-3" /> {new Date(ticket.lastMessageAt).toLocaleDateString()}</span>
                                     <span className="font-mono opacity-70">#{ticket.id.slice(-4)}</span>
                                 </div>
                             </div>
                         ))}
                         {tickets.length === 0 && !isCreating && (
                            <div className="text-center py-12 text-slate-400">
                                <MessageSquare className="w-12 h-12 mx-auto mb-2 opacity-20" />
                                <p className="text-sm">No tickets found.</p>
                            </div>
                         )}
                     </div>
                 </Card>
            </div>
            {/* Chat Area */}
            <div className="md:col-span-2">
                <Card className="h-full flex flex-col p-0 overflow-hidden">
                    {selectedTicket ? (
                        <>
                            <div className="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                                <div>
                                    <h3 className="font-bold text-slate-800 flex items-center gap-2">
                                        {selectedTicket.subject}
                                        {selectedTicket.status === 'CLOSED' && <Lock className="w-3.5 h-3.5 text-slate-400" />}
                                    </h3>
                                    <p className="text-xs text-slate-500">Ticket ID: {selectedTicket.id}</p>
                                </div>
                                <StatusBadge status={selectedTicket.status} />
                            </div>
                            <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/30">
                                {messages.map(msg => (
                                    <div key={msg.id} className={`flex ${msg.isAdmin ? 'justify-start' : 'justify-end'}`}>
                                        <div className={`max-w-[80%] rounded-2xl px-4 py-3 text-sm ${msg.isAdmin ? 'bg-white border border-slate-200 text-slate-700 rounded-tl-none shadow-sm' : 'bg-indigo-600 text-white rounded-tr-none shadow-md'}`}>
                                            <p className="mb-1">{msg.text}</p>
                                            <div className={`text-[10px] ${msg.isAdmin ? 'text-slate-400' : 'text-indigo-200'} text-right`}>{new Date(msg.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                                        </div>
                                    </div>
                                ))}
                                {messages.length === 0 && <div className="text-center py-12 text-slate-400"><MessageSquare className="w-12 h-12 mx-auto mb-2 opacity-20" /><p>Start the conversation...</p></div>}
                                <div ref={chatEndRef} />
                            </div>
                            {selectedTicket.status === 'OPEN' ? (
                                <form onSubmit={handleSendMessage} className="p-4 border-t border-slate-100 bg-white flex gap-2">
                                    <input type="text" value={newMessage} onChange={e => setNewMessage(e.target.value)} placeholder="Type your message..." className="flex-1 px-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                                    <button type="submit" disabled={!newMessage.trim()} className="p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"><Send className="w-5 h-5" /></button>
                                </form>
                            ) : (
                                <div className="p-4 border-t border-slate-100 bg-slate-100 text-center text-sm text-slate-500 flex items-center justify-center gap-2">
                                    <Lock className="w-4 h-4" /> This ticket is closed.
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="h-full flex flex-col items-center justify-center text-slate-400"><MessageSquare className="w-16 h-16 mb-4 opacity-20" /><p>Select a ticket to view conversation</p></div>
                    )}
                </Card>
            </div>
        </div>
    );
};