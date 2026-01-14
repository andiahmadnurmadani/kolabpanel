import React, { useState, useEffect, useRef } from 'react';
import { Card, StatusBadge } from '../../components/Shared';
import { api } from '../../services/api';
import { SupportTicket, ChatMessage } from '../../types';
import { Send, MessageSquare } from 'lucide-react';

export const AdminSupport: React.FC = () => {
    const [tickets, setTickets] = useState<SupportTicket[]>([]);
    const [selectedTicketId, setSelectedTicketId] = useState<string | null>(null);
    const [messages, setMessages] = useState<ChatMessage[]>([]);
    const [newMessage, setNewMessage] = useState('');
    const chatEndRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        loadTickets();
    }, []);

    useEffect(() => {
        if (selectedTicketId) {
            loadMessages(selectedTicketId);
        }
    }, [selectedTicketId]);

    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    const loadTickets = async () => {
        const data = await api.tickets.list(); // Load all for admin
        setTickets(data);
    };

    const loadMessages = async (ticketId: string) => {
        const data = await api.tickets.getMessages(ticketId);
        setMessages(data);
    };

    const handleSendMessage = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!newMessage.trim() || !selectedTicketId) return;
        
        // Use a static admin ID for now
        const sent = await api.tickets.sendMessage(selectedTicketId, 'admin_id', 'Support Agent', newMessage, true);
        setMessages([...messages, sent]);
        setNewMessage('');
    };

    const handleCloseTicket = async () => {
        if (selectedTicketId && confirm("Close this ticket?")) {
            await api.tickets.close(selectedTicketId);
            loadTickets(); // Refresh status
        }
    }

    const selectedTicket = tickets.find(t => t.id === selectedTicketId);

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 h-[calc(100vh-140px)] animate-in fade-in duration-300">
            {/* Ticket List */}
            <div className="md:col-span-1 flex flex-col gap-4">
                 <Card className="h-full flex flex-col">
                     <h3 className="font-bold text-slate-800 mb-4">Incoming Tickets</h3>
                     <div className="flex-1 overflow-y-auto pr-2 space-y-2">
                         {tickets.map(ticket => (
                             <div 
                                key={ticket.id}
                                onClick={() => setSelectedTicketId(ticket.id)}
                                className={`p-3 rounded-lg border cursor-pointer transition-all ${selectedTicketId === ticket.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-slate-300'}`}
                             >
                                 <div className="flex justify-between items-start mb-1">
                                     <span className="font-bold text-xs text-indigo-600 truncate uppercase">{ticket.username}</span>
                                     <StatusBadge status={ticket.status} />
                                 </div>
                                 <div className="font-medium text-sm text-slate-800 truncate mb-1">{ticket.subject}</div>
                                 <div className="text-xs text-slate-400">
                                     {new Date(ticket.lastMessageAt).toLocaleString()}
                                 </div>
                             </div>
                         ))}
                         {tickets.length === 0 && (
                             <div className="text-center py-8 text-slate-400 text-sm">No tickets found.</div>
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
                                    <h3 className="font-bold text-slate-800">{selectedTicket.subject}</h3>
                                    <p className="text-xs text-slate-500">User: {selectedTicket.username} | ID: {selectedTicket.id}</p>
                                </div>
                                <div className="flex items-center gap-2">
                                     <StatusBadge status={selectedTicket.status} />
                                     {selectedTicket.status === 'OPEN' && (
                                         <button onClick={handleCloseTicket} className="text-xs px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100 text-slate-600">
                                             Close Ticket
                                         </button>
                                     )}
                                </div>
                            </div>
                            
                            <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/30">
                                {messages.map(msg => (
                                    <div key={msg.id} className={`flex ${msg.isAdmin ? 'justify-end' : 'justify-start'}`}>
                                        <div className={`max-w-[80%] rounded-2xl px-4 py-3 text-sm ${
                                            msg.isAdmin 
                                            ? 'bg-indigo-600 text-white rounded-tr-none shadow-md'
                                            : 'bg-white border border-slate-200 text-slate-700 rounded-tl-none shadow-sm' 
                                        }`}>
                                            {!msg.isAdmin && <div className="text-xs font-bold text-slate-500 mb-1">{msg.senderName}</div>}
                                            <p className="mb-1">{msg.text}</p>
                                            <div className={`text-[10px] ${msg.isAdmin ? 'text-indigo-200' : 'text-slate-400'} text-right`}>
                                                {new Date(msg.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                                <div ref={chatEndRef} />
                            </div>

                            {selectedTicket.status === 'OPEN' ? (
                                <form onSubmit={handleSendMessage} className="p-4 border-t border-slate-100 bg-white flex gap-2">
                                    <input 
                                        type="text" 
                                        value={newMessage}
                                        onChange={e => setNewMessage(e.target.value)}
                                        placeholder="Reply to user..."
                                        className="flex-1 px-4 py-2 border border-slate-300 rounded-full focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                                    />
                                    <button 
                                        type="submit"
                                        disabled={!newMessage.trim()} 
                                        className="p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <Send className="w-5 h-5" />
                                    </button>
                                </form>
                            ) : (
                                <div className="p-4 border-t border-slate-100 bg-slate-50 text-center text-sm text-slate-500">
                                    This ticket is closed.
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="h-full flex flex-col items-center justify-center text-slate-400">
                            <MessageSquare className="w-16 h-16 mb-4 opacity-20" />
                            <p>Select a ticket to reply</p>
                        </div>
                    )}
                </Card>
            </div>
        </div>
    );
}
