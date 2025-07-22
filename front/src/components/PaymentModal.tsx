import { useState } from 'react';
import { api } from '../services/api';

interface PaymentModalProps {
  isOpen: boolean;
  onClose: () => void;
  paymentId: number;
  amount: string;
  planDescription: string;
  onPaymentConfirmed: () => void;
}

export function PaymentModal({ 
  isOpen, 
  onClose, 
  paymentId, 
  amount, 
  planDescription,
  onPaymentConfirmed 
}: PaymentModalProps) {
  const [pixData, setPixData] = useState<any>(null);
  const [loading, setLoading] = useState(false);
  const [confirming, setConfirming] = useState(false);

  const generatePix = async () => {
    setLoading(true);
    try {
      const data = await api.generatePix(paymentId);
      setPixData(data);
    } catch (error) {
      console.error('Erro ao gerar PIX:', error);
    } finally {
      setLoading(false);
    }
  };

  const confirmPayment = async () => {
    setConfirming(true);
    try {
      await api.confirmPayment(paymentId);
      onPaymentConfirmed();
      onClose();
    } catch (error) {
      console.error('Erro ao confirmar pagamento:', error);
    } finally {
      setConfirming(false);
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 className="text-xl font-bold mb-4">Pagamento via PIX</h2>
        
        <div className="mb-4">
          <p className="text-sm text-gray-600 mb-2">Plano selecionado:</p>
          <p className="font-medium">{planDescription}</p>
        </div>

        <div className="mb-6">
          <p className="text-sm text-gray-600 mb-2">Valor a pagar:</p>
          <p className="text-2xl font-bold text-green-600">
            R$ {parseFloat(amount).toFixed(2).replace('.', ',')}
          </p>
        </div>

        {!pixData ? (
          <button
            onClick={generatePix}
            disabled={loading}
            className="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded font-medium disabled:opacity-50"
          >
            {loading ? 'Gerando PIX...' : 'Gerar PIX'}
          </button>
        ) : (
          <div className="space-y-4">
            <div className="border rounded p-4 bg-gray-50">
              <p className="text-sm text-gray-600 mb-2">Chave PIX:</p>
              <p className="font-mono text-sm break-all">{pixData.pix_key}</p>
            </div>
            
            <div className="text-center">
              <div className="w-32 h-32 bg-gray-200 mx-auto mb-2 flex items-center justify-center">
                <span className="text-xs text-gray-500">QR Code</span>
              </div>
              <p className="text-xs text-gray-500">
                Escaneie o QR Code ou use a chave PIX acima
              </p>
            </div>

            <button
              onClick={confirmPayment}
              disabled={confirming}
              className="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded font-medium disabled:opacity-50"
            >
              {confirming ? 'Confirmando...' : 'Confirmar Pagamento'}
            </button>
          </div>
        )}

        <button
          onClick={onClose}
          className="w-full mt-4 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded font-medium"
        >
          Cancelar
        </button>
      </div>
    </div>
  );
}