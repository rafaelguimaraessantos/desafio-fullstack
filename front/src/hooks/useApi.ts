import { useState, useEffect } from 'react';
import { api, Plan, User, Contract } from '../services/api';

export function usePlans() {
  const [plans, setPlans] = useState<Plan[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function fetchPlans() {
      try {
        const data = await api.getPlans();
        setPlans(data);
      } catch (err) {
        setError('Erro ao carregar planos');
      } finally {
        setLoading(false);
      }
    }

    fetchPlans();
  }, []);

  return { plans, loading, error };
}

export function useUser() {
  const [user, setUser] = useState<User | null>(null);
  const [activeContract, setActiveContract] = useState<Contract | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchUser = async () => {
    try {
      setLoading(true);
      const data = await api.getUser();
      setUser(data.user);
      setActiveContract(data.active_contract);
    } catch (err) {
      setError('Erro ao carregar usuário');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchUser();
  }, []);

  return { user, activeContract, loading, error, refetch: fetchUser };
}

export function useContractHistory(userId: number) {
  const [contracts, setContracts] = useState<Contract[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchHistory = async () => {
    if (!userId) return;
    
    try {
      setLoading(true);
      const data = await api.getContractHistory(userId);
      setContracts(data);
      setError(null);
    } catch (err) {
      setError('Erro ao carregar histórico');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchHistory();
  }, [userId]);

  return { contracts, loading, error, refetch: fetchHistory };
}