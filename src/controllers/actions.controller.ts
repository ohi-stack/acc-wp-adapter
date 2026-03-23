import { Request, Response } from 'express';
import { wordpressExecute } from '../adapters/wordpress.adapter';

export async function handleAction(req: Request, res: Response) {
  try {
    const { action, payload } = req.body;

    if (!action) {
      return res.status(400).json({ error: 'Missing action' });
    }

    const result = await wordpressExecute(action, payload);

    return res.json({
      status: 'completed',
      action,
      result
    });
  } catch (err: any) {
    return res.status(500).json({
      status: 'error',
      message: err.message
    });
  }
}
