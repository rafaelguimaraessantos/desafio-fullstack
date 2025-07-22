const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export interface Plan {
  id: number;
  description: string;
  numberOfClients: number;
  gigabytesStorage: number;
  price: string;
  active: boolean;
}

export interface User {
  id: number;
  name: string;
  email: string;
}

export interface Contract {
  id: number;
  user_id: number;
  plan_id: number;
  active: boolean;
  contract_date: string;
  plan: Plan;
}

export interface Payment {
  id: number;
  contract_id: number;
  amount: string;
  discount: string;
  status: 'pending' | 'paid' | 'cancelled';
  due_date: string;
  paid_at?: string;
}

export const api = {
  async getPlans(): Promise<Plan[]> {
    const response = await fetch(`${API_URL}/plans`);
    return response.json();
  },

  async getUser(): Promise<{ user: User; active_contract: Contract | null }> {
    const response = await fetch(`${API_URL}/user`);
    return response.json();
  },

  async createContract(planId: number, userId: number) {
    const response = await fetch(`${API_URL}/contracts`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        plan_id: planId,
        user_id: userId,
      }),
    });
    return response.json();
  },

  async getContractHistory(userId: number): Promise<Contract[]> {
    const response = await fetch(`${API_URL}/contracts/${userId}/history`);
    return response.json();
  },

  async generatePix(paymentId: number) {
    const response = await fetch(`${API_URL}/payments/pix`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        payment_id: paymentId,
      }),
    });
    return response.json();
  },

  async confirmPayment(paymentId: number) {
    const response = await fetch(`${API_URL}/payments/confirm`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        payment_id: paymentId,
      }),
    });
    return response.json();
  },
};