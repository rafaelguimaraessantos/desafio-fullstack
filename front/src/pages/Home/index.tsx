import { useState } from 'react';
import { usePlans, useUser, useContractHistory } from '../../hooks/useApi';
import { PlanCard } from '../../components/PlanCard';
import { PaymentModal } from '../../components/PaymentModal';
import { api } from '../../services/api';

export const Home = () => {
  const { plans, loading: plansLoading } = usePlans();
  const { user, activeContract, loading: userLoading, refetch: refetchUser } = useUser();
  const { contracts } = useContractHistory(user?.id || 0);
  
  const [selectedPlan, setSelectedPlan] = useState<number | null>(null);
  const [paymentModal, setPaymentModal] = useState<{
    isOpen: boolean;
    paymentId: number;
    amount: string;
    planDescription: string;
  }>({
    isOpen: false,
    paymentId: 0,
    amount: '0',
    planDescription: '',
  });
  const [processing, setProcessing] = useState(false);

  const handlePlanSelect = async (planId: number) => {
    if (!user || processing) return;
    
    setProcessing(true);
    try {
      const result = await api.createContract(planId, user.id);
      
      // Simular que temos um payment_id (normalmente viria da resposta)
      const paymentId = Date.now(); // Simulação
      const selectedPlanData = plans.find(p => p.id === planId);
      
      setPaymentModal({
        isOpen: true,
        paymentId,
        amount: result.payment_amount || selectedPlanData?.price || '0',
        planDescription: selectedPlanData?.description || '',
      });
    } catch (error) {
      console.error('Erro ao criar contrato:', error);
    } finally {
      setProcessing(false);
    }
  };

  const handlePaymentConfirmed = () => {
    refetchUser();
  };

  if (plansLoading || userLoading) {
    return (
      <div className="flex items-center justify-center h-screen">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto mb-4"></div>
          <p className="text-gray-600">Carregando...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container mx-auto px-4 py-8">
        {/* Header */}
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-gray-800 mb-2">
            Sistema de Assinatura de Planos
          </h1>
          <p className="text-gray-600">Desafio Full-stack - Inmediam</p>
        </div>

        {/* User Info */}
        {user && (
          <div className="bg-white rounded-lg shadow p-6 mb-8">
            <h2 className="text-xl font-semibold mb-4">Informações do Usuário</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p className="text-sm text-gray-600">Nome:</p>
                <p className="font-medium">{user.name}</p>
              </div>
              <div>
                <p className="text-sm text-gray-600">Email:</p>
                <p className="font-medium">{user.email}</p>
              </div>
            </div>
            
            {activeContract && (
              <div className="mt-4 p-4 bg-blue-50 rounded">
                <p className="text-sm text-blue-600 font-medium mb-2">Plano Atual:</p>
                <p className="font-semibold">{activeContract.plan.description}</p>
                <p className="text-sm text-gray-600">
                  R$ {parseFloat(activeContract.plan.price).toFixed(2).replace('.', ',')}/mês
                </p>
              </div>
            )}
          </div>
        )}

        {/* Plans */}
        <div className="mb-8">
          <h2 className="text-2xl font-semibold mb-6 text-center">
            {activeContract ? 'Trocar de Plano' : 'Escolha seu Plano'}
          </h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {plans.map((plan) => (
              <PlanCard
                key={plan.id}
                plan={plan}
                isActive={activeContract?.plan_id === plan.id}
                onSelect={handlePlanSelect}
                disabled={processing || activeContract?.plan_id === plan.id}
              />
            ))}
          </div>
        </div>

        {/* Contract History */}
        {contracts.length > 0 && (
          <div className="bg-white rounded-lg shadow p-6">
            <h2 className="text-xl font-semibold mb-4">Histórico de Contratos</h2>
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b">
                    <th className="text-left py-2">Plano</th>
                    <th className="text-left py-2">Valor</th>
                    <th className="text-left py-2">Data do Contrato</th>
                    <th className="text-left py-2">Status</th>
                  </tr>
                </thead>
                <tbody>
                  {contracts.map((contract) => (
                    <tr key={contract.id} className="border-b">
                      <td className="py-2">{contract.plan.description}</td>
                      <td className="py-2">
                        R$ {parseFloat(contract.plan.price).toFixed(2).replace('.', ',')}
                      </td>
                      <td className="py-2">
                        {new Date(contract.contract_date).toLocaleDateString('pt-BR')}
                      </td>
                      <td className="py-2">
                        <span className={`px-2 py-1 rounded text-xs ${
                          contract.active 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-gray-100 text-gray-800'
                        }`}>
                          {contract.active ? 'Ativo' : 'Inativo'}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>

      <PaymentModal
        isOpen={paymentModal.isOpen}
        onClose={() => setPaymentModal(prev => ({ ...prev, isOpen: false }))}
        paymentId={paymentModal.paymentId}
        amount={paymentModal.amount}
        planDescription={paymentModal.planDescription}
        onPaymentConfirmed={handlePaymentConfirmed}
      />
    </div>
  );
};