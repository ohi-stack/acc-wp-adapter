import { Router } from 'express';
import { handleAction } from '../controllers/actions.controller';

const router = Router();

router.post('/', handleAction);

export default router;
